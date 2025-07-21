<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    /**
     * Menampilkan daftar project dengan fungsionalitas pencarian dan pengurutan.
     * Dapat diakses oleh semua user terautentikasi.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Project::class); // Menggunakan ProjectPolicy@viewAny

        $query = Project::query();

        // Fungsionalitas Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('subSystems', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('subSystems.sites', function($ssq) use ($search) {
                      $ssq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Fungsionalitas Pengurutan
        $sortColumn = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        if (!in_array($sortColumn, ['name', 'start_date', 'end_date'])) {
            $sortColumn = 'name';
        }
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $query->orderBy($sortColumn, $sortDirection);

        $projects = $query->with('subSystems.sites')->get();

        return view('projects.index', compact('projects', 'sortColumn', 'sortDirection'));
    }

    /**
     * Menampilkan form untuk membuat project baru.
     * Hanya admin yang bisa mengakses.
     */
    public function create()
    {
        $this->authorize('create', Project::class);
        return view('projects.create');
    }

    /**
     * Menyimpan project baru ke storage.
     * Hanya admin yang bisa melakukan ini.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Project::class);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        Project::create($request->all());

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    /**
     * Menampilkan detail project yang ditentukan.
     * Dapat diakses oleh semua user terautentikasi.
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);
        $project->load('subSystems.sites', 'assignedUsers');
        return view('projects.show', compact('project'));
    }

    /**
     * Menampilkan form untuk mengedit project yang ditentukan.
     * Hanya admin yang bisa mengakses.
     */
    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        $engineers = User::where('role', 'engineer')->orderBy('name')->get();
        $project->load('assignedUsers');

        return view('projects.edit', compact('project', 'engineers'));
    }

    /**
     * Memperbarui project yang ditentukan di storage.
     * Hanya admin yang bisa melakukan ini.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'assigned_engineers' => 'nullable|array',
            'assigned_engineers.*' => 'exists:users,id',
        ]);

        $project->update($request->all());

        $project->assignedUsers()->sync($request->assigned_engineers);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    /**
     * Menghapus project yang ditentukan dari storage.
     * Hanya admin yang bisa melakukan ini.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

    /**
     * Menampilkan laporan selesai untuk proyek yang sudah lewat deadline.
     * Dapat diakses oleh Admin dan Engineer yang ditugaskan.
     */
    public function completionReport(Project $project)
    {
        // Otorisasi: Admin atau Engineer yang ditugaskan ke proyek ini
        if (!Auth::user()->isAdmin() && !Auth::user()->assignedProjects->contains($project->id)) {
            abort(403, 'Anda tidak memiliki izin untuk melihat laporan proyek ini.');
        }

        // Hitung total SS
        $totalSubSystems = $project->subSystems->count();

        // Hitung total masing-masing kategori file yang sudah diupload engineer
        $documentCategoryCounts = [];
        $allDocumentTypes = Document::all()->pluck('name', 'id')->toArray();

        foreach ($project->subSystems as $subSystem) {
            foreach ($subSystem->sites as $site) {
                foreach ($site->siteDocuments as $siteDocument) {
                    $documentCategory = $siteDocument->document->name;
                    if (!isset($documentCategoryCounts[$documentCategory])) {
                        $documentCategoryCounts[$documentCategory] = 0;
                    }
                    $documentCategoryCounts[$documentCategory]++;
                }
            }
        }

        foreach ($allDocumentTypes as $id => $name) {
            if (!isset($documentCategoryCounts[$name])) {
                $documentCategoryCounts[$name] = 0;
            }
        }
        ksort($documentCategoryCounts);

        $subSystemCompletionStatus = [];
        foreach ($project->subSystems as $subSystem) {
            $sitesCompletion = [];
            foreach ($subSystem->sites as $site) {
                $sitesCompletion[] = [
                    'name' => $site->name,
                    'progress_percentage' => $site->progress_percentage,
                    'all_required_files_uploaded' => $site->allRequiredFilesUploaded(),
                    'deadline_status' => $site->deadline_status,
                    'days_overdue' => $site->days_overdue,
                ];
            }
            $subSystemCompletionStatus[] = [
                'name' => $subSystem->name,
                'progress_percentage' => $subSystem->progress_percentage,
                'sites_completion' => $sitesCompletion,
            ];
        }


        return view('projects.completion_report', compact('project', 'totalSubSystems', 'documentCategoryCounts', 'subSystemCompletionStatus'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Project::class);

        $query = Project::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            // Memastikan pencarian lebih aman dan tidak error jika relasi tidak ada
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('subSystems', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%')
                         ->orWhereHas('sites', function($ssq) use ($search) {
                             $ssq->where('name', 'like', '%' . $search . '%');
                         });
                  });
            });
        }

        $sortColumn = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        // Validasi kolom pengurutan
        $validSortColumns = ['name', 'start_date', 'end_date'];
        if (!in_array($sortColumn, $validSortColumns)) {
            $sortColumn = 'name';
        }
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        // Mengurutkan dengan aman, menempatkan nilai NULL di akhir
        if ($sortColumn === 'start_date' || $sortColumn === 'end_date') {
            $query->orderByRaw("ISNULL($sortColumn) $sortDirection, $sortColumn $sortDirection");
        } else {
            $query->orderBy($sortColumn, $sortDirection);
        }


        $projects = $query->with('subSystems.sites')->get();

        return view('projects.index', compact('projects', 'sortColumn', 'sortDirection'));
    }

    public function create()
    {
        $this->authorize('create', Project::class);
        return view('projects.create');
    }

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

    public function show(Project $project)
    {
        $this->authorize('view', $project);
        $project->load('subSystems.sites', 'assignedUsers');
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        $engineers = User::where('role', 'engineer')->orderBy('name')->get();
        $project->load('assignedUsers');

        return view('projects.edit', compact('project', 'engineers'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'assigned_engineers' => 'nullable|array',
            'assigned_engineers.*' => 'exists:users,id',
        ]);

        $project->update($request->all());

        if ($request->has('assigned_engineers')) {
            $project->assignedUsers()->sync($request->assigned_engineers);
        } else {
            $project->assignedUsers()->sync([]);
        }

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
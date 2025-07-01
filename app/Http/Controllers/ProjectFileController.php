<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class ProjectFileController extends Controller
{
    use AuthorizesRequests;

    public function index(Project $project)
    {
        $projectFiles = $project->projectFiles()->with('uploader')->get();
        return view('projects.files.index', compact('project', 'projectFiles'));
    }

    public function allProjectFiles()
    {
        $projectFiles = ProjectFile::with('project', 'uploader')->get();
        return view('project_files.index', compact('projectFiles'));
    }

    public function store(Request $request, Project $project)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $filePath = $request->file('file')->store('project_files', 'public');

        $project->projectFiles()->create([
            'name' => $request->name,
            'file_path' => $filePath,
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'File uploaded successfully.');
    }

    public function download(ProjectFile $projectFile)
    {
        return Storage::disk('public')->download($projectFile->file_path, $projectFile->name . '.' . pathinfo($projectFile->file_path, PATHINFO_EXTENSION));
    }

    public function view(ProjectFile $projectFile)
    {
        $fileExtension = pathinfo($projectFile->file_path, PATHINFO_EXTENSION);
        $mimeType = Storage::disk('public')->mimeType($projectFile->file_path);

        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'pdf'])) {
            $file = Storage::disk('public')->get($projectFile->file_path);
            return response($file, 200)->header('Content-Type', $mimeType);
        }

        return redirect()->back()->with('error', 'Tipe file tidak didukung untuk dilihat. Silakan unduh.');
    }

    public function destroy(Project $project, ProjectFile $projectFile)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        Storage::disk('public')->delete($projectFile->file_path);
        $projectFile->delete();

        return redirect()->back()->with('success', 'File deleted successfully.');
    }
}
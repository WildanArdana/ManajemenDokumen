<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\SubSystem;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SubSystemController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Project $project)
    {
        $this->authorize('create', SubSystem::class);
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project->subSystems()->create($request->all());

        return redirect()->route('projects.show', $project)->with('success', 'Sub System added successfully.');
    }

    public function show(SubSystem $subSystem)
    {
        $this->authorize('view', $subSystem);
        $subSystem->load('sites');
        return view('sub_systems.show', compact('subSystem'));
    }

    public function edit(SubSystem $subSystem)
    {
        $this->authorize('manage', $subSystem);
        return view('sub_systems.edit', compact('subSystem'));
    }

    public function update(Request $request, SubSystem $subSystem)
    {
        $this->authorize('manage', $subSystem);
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $subSystem->update($request->all());
        return redirect()->route('projects.show', $subSystem->project)->with('success', 'Sub System updated successfully.');
    }

    public function destroy(SubSystem $subSystem)
    {
        $this->authorize('manage', $subSystem);
        $project = $subSystem->project;
        $subSystem->delete();
        return redirect()->route('projects.show', $project)->with('success', 'Sub System deleted successfully.');
    }
}
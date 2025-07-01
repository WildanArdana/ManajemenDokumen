<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Site;
use App\Models\Document;
use App\Models\SubSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SiteController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, SubSystem $subSystem)
    {
        $this->authorize('manage', Site::class);
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
        ]);

        $subSystem->sites()->create($request->all());

        return redirect()->route('sub_systems.show', $subSystem)->with('success', 'Site added successfully.');
    }

    public function show(Site $site)
    {
        $this->authorize('view', $site);

        $site->load('siteDocuments.document', 'siteDocuments.uploader', 'comments.user', 'subSystem.project.assignedUsers');
        $documents = Document::all();
        $uploadedDocuments = $site->siteDocuments->groupBy('document_id');

        $totalRequired = $documents->count();
        $uploadedCount = $site->siteDocuments->unique('document_id')->count();
        $percentage = $totalRequired > 0 ? round(($uploadedCount / $totalRequired) * 100) : 0;

        $canPerformActions = Auth::user()->can('performActions', $site);

        return view('sites.show', compact('site', 'documents', 'uploadedDocuments', 'percentage', 'canPerformActions'));
    }

    public function edit(Site $site)
    {
        $this->authorize('manage', $site);
        return view('sites.edit', compact('site'));
    }

    public function update(Request $request, Site $site)
    {
        $this->authorize('manage', $site);
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
        ]);

        $site->update($request->all());

        return redirect()->route('sub_systems.show', $site->subSystem)->with('success', 'Site updated successfully.');
    }

    public function destroy(Site $site)
    {
        $this->authorize('manage', $site);
        $subSystem = $site->subSystem;
        $site->delete();
        return redirect()->route('sub_systems.show', $subSystem)->with('success', 'Site deleted successfully.');
    }
}
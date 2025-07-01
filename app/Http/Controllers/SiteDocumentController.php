<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Document;
use App\Models\SiteDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SiteDocumentController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Site $site)
    {
        $this->authorize('uploadDocument', $site);

        $request->validate([
            'document_id' => ['required', 'exists:documents,id'],
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $existingDocument = $site->siteDocuments()
                                ->where('document_id', $request->document_id)
                                ->where('uploaded_by', Auth::id())
                                ->first();

        if ($existingDocument) {
            Storage::disk('public')->delete($existingDocument->file_path);
            $filePath = $request->file('file')->store('site_documents', 'public');
            $existingDocument->update([
                'file_path' => $filePath,
                'uploaded_at' => now(),
            ]);
            $message = 'Dokumen berhasil diperbarui.';
        } else {
            $filePath = $request->file('file')->store('site_documents', 'public');
            $site->siteDocuments()->create([
                'document_id' => $request->document_id,
                'file_path' => $filePath,
                'uploaded_by' => Auth::id(),
                'uploaded_at' => now(),
            ]);
            $message = 'Dokumen berhasil diunggah.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function download(SiteDocument $siteDocument)
    {
        return Storage::disk('public')->download($siteDocument->file_path, $siteDocument->document->name . '.' . pathinfo($siteDocument->file_path, PATHINFO_EXTENSION));
    }

    public function view(SiteDocument $siteDocument)
    {
        $fileExtension = pathinfo($siteDocument->file_path, PATHINFO_EXTENSION);
        $mimeType = Storage::disk('public')->mimeType($siteDocument->file_path);

        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'pdf'])) {
            $file = Storage::disk('public')->get($siteDocument->file_path);
            return response($file, 200)->header('Content-Type', $mimeType);
        }

        return redirect()->back()->with('error', 'Tipe file tidak didukung untuk dilihat. Silakan unduh.');
    }

    public function destroy(SiteDocument $siteDocument)
    {
        $this->authorize('deleteDocument', [$siteDocument->site, $siteDocument]);

        Storage::disk('public')->delete($siteDocument->file_path);
        $siteDocument->delete();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
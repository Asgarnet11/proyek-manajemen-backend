<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectDocumentController extends Controller
{
    /**
     * Menampilkan daftar dokumen untuk sebuah proyek.
     */
    public function index(Project $project)
    {
        return $project->documents()->with('uploader:id,name')->get();
    }

    /**
     * Menyimpan file yang di-upload.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,docx,mp4|max:30480'
        ]);

        $file = $request->file('file');
        $path = $file->store('project_documents', 'public');

        $document = $project->documents()->create([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_mime_type' => $file->getMimeType(),
            'uploaded_by' => Auth::id(),
        ]);

        return response()->json($document, 201);
    }

    /**
     * Menghapus sebuah dokumen.
     */
    public function destroy(ProjectDocument $projectDocument)
    {
        Storage::disk('public')->delete($projectDocument->file_path);

        $projectDocument->delete();

        return response()->json(null, 204);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProgressUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProgressUpdateController extends Controller
{
    /**
     * Menampilkan daftar semua progress update untuk sebuah proyek.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function index(Project $project)
    {
        // Mengambil semua progress update terkait proyek, memuat relasi pembuatnya,
        // dan mengurutkannya dari yang paling baru.
        return $project->progressUpdates()->with('creator:id,name')->latest('update_date')->get();
    }

    /**
     * Menyimpan data progress update baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'percentage' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
            'update_date' => 'required|date',
            'photo' => 'nullable|image|max:5120', // Foto opsional, max 5MB
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            // Simpan foto ke storage/app/public/progress_photos
            $photoPath = $request->file('photo')->store('progress_photos', 'public');
        }

        $progressUpdate = $project->progressUpdates()->create([
            'percentage' => $validated['percentage'],
            'notes' => $validated['notes'],
            'update_date' => $validated['update_date'],
            'photo_path' => $photoPath,
            'created_by' => Auth::id(),
        ]);

        return response()->json($progressUpdate->load('creator:id,name'), 201);
    }

    /**
     * Menampilkan detail satu progress update.
     *
     * @param  \App\Models\ProgressUpdate  $progressUpdate
     * @return \Illuminate\Http\Response
     */
    public function show(ProgressUpdate $progressUpdate)
    {
        // Memuat relasi pembuat (creator) dan proyek (project)
        return $progressUpdate->load('creator:id,name', 'project:id,name');
    }

    /**
     * Menghapus sebuah progress update.
     *
     * @param  \App\Models\ProgressUpdate  $progressUpdate
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProgressUpdate $progressUpdate)
    {
        // Cek jika ada file foto yang terhubung, hapus dari storage
        if ($progressUpdate->photo_path) {
            Storage::disk('public')->delete($progressUpdate->photo_path);
        }

        // Hapus record dari database
        $progressUpdate->delete();

        return response()->json(null, 204); // 204 No Content
    }
}

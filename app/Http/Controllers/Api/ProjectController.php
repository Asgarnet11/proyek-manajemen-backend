<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class ProjectController extends Controller
{
    // Role yang diizinkan membuat/mengubah/menghapus
    private function hasAccess(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return in_array(auth()->user()->role, ['admin', 'manajer_proyek', 'supervisor']);
    }

    /**
     * Menampilkan semua proyek
     */
    public function index()
    {
        return Project::with('pic:id,name')->get();
    }

    /**
     * Menyimpan proyek baru
     */
    public function store(Request $request)
    {
        if (!$this->hasAccess()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'type' => 'required|string',
            'status' => 'required|string',
            'description' => 'nullable|string',
            'client_name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'dokumen_path' => 'nullable|string',
        ]);

        $project = Project::create($validated + ['pic_id' => Auth::id()]);

        return response()->json($project, 201);
    }

    /**
     * Menampilkan detail proyek
     */
    public function show(Project $project)
    {
        return $project->load('pic:id,name,email');
    }

    /**
     * Update proyek
     */
    public function update(Request $request, Project $project)
    {
        if (!$this->hasAccess()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string',
            'status' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'client_name' => 'sometimes|required|string',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'dokumen_path' => 'nullable|string',
        ]);

        $project->update($validated);

        return response()->json($project);
    }

    /**
     * Hapus proyek
     */
    public function destroy(Project $project)
    {
        if (!$this->hasAccess()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $project->delete();
        return response()->json(null, 204);
    }
}

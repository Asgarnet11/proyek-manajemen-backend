<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Menampilkan daftar semua tugas untuk sebuah proyek.
     */
    public function index(Project $project)
    {
        return $project->tasks()->with('user:id,name')->get();
    }

    /**
     * Menyimpan tugas baru untuk sebuah proyek.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id', // Penanggung Jawab
            'priority' => 'required|string',
        ]);

        $task = $project->tasks()->create($validated);

        return response()->json($task, 201);
    }

    /**
     * Menampilkan detail satu tugas.
     */
    public function show(Task $task)
    {
        return $task->load('user:id,name', 'project:id,name');
    }

    /**
     * Mengupdate data tugas.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'user_id' => 'sometimes|required|exists:users,id',
            'priority' => 'sometimes|required|string',
            'status' => 'sometimes|required|string', // Status tugas
        ]);

        $task->update($validated);
        return response()->json($task);
    }

    /**
     * Menghapus tugas.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(null, 204);
    }
}

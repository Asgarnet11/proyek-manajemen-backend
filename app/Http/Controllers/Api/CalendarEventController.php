<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

class CalendarEventController extends Controller
{
    /**
     * Menampilkan daftar event untuk sebuah proyek.
     */
    public function index(Project $project)
    {
        return $project->calendarEvents()->with('creator:id,name')->get();
    }

    /**
     * Menyimpan event baru.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
        ]);

        $event = $project->calendarEvents()->create($validated + ['created_by' => Auth::id()]);

        return response()->json($event, 201);
    }

    /**
     * Menampilkan detail satu event.
     */
    public function show(CalendarEvent $calendarEvent)
    {
        return $calendarEvent->load('creator:id,name', 'project:id,name');
    }

    /**
     * Mengupdate sebuah event.
     */
    public function update(Request $request, CalendarEvent $calendarEvent)
    {
        $user = Auth::user();

        // Hanya pembuat event, Admin, atau Manajer Proyek yang boleh mengedit
        if ($user->id !== $calendarEvent->created_by && !in_array($user->role, ['Admin', 'Manajer Proyek'])) {
            throw new AuthorizationException('Anda tidak memiliki izin untuk mengedit event ini.');
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date|after_or_equal:start_time',
        ]);

        $calendarEvent->update($validated);
        return response()->json($calendarEvent);
    }

    /**
     * Menghapus sebuah event.
     */
    public function destroy(CalendarEvent $calendarEvent)
    {
        $user = Auth::user();

        // Hanya pembuat event, Admin, atau Manajer Proyek yang boleh menghapus
        if ($user->id !== $calendarEvent->created_by && !in_array($user->role, ['Admin', 'Manajer Proyek'])) {
            throw new AuthorizationException('Anda tidak memiliki izin untuk menghapus event ini.');
        }

        $calendarEvent->delete();
        return response()->json(null, 204);
    }
}

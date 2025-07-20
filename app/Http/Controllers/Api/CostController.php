<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cost;
use App\Models\Project;
use Illuminate\Http\Request;

class CostController extends Controller
{
    /**
     * Menampilkan daftar biaya untuk sebuah proyek.
     */
    public function index(Project $project)
    {
        return $project->costs()->latest()->get();
    }

    /**
     * Menyimpan item biaya baru.
     */
    public function store(Request $request, Project $project)
    {
        if (!in_array(auth()->user()->role, ['admin', 'finance'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:Anggaran,Realisasi',
            'vendor_name' => 'nullable|string',
        ]);

        $cost = $project->costs()->create($validated + ['status' => 'Pending']);

        return response()->json($cost, 201);
    }

    /**
     * Menampilkan detail satu item biaya.
     */
    public function show(Cost $cost)
    {
        return $cost;
    }

    /**
     * Mengupdate item biaya.
     */
    public function update(Request $request, Cost $cost)
    {
        $validated = $request->validate([
            'description' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'type' => 'sometimes|required|in:Anggaran,Realisasi',
            'vendor_name' => 'nullable|string',
        ]);

        $cost->update($validated);
        return response()->json($cost);
    }

    /**
     * Menghapus item biaya.
     */
    public function destroy(Cost $cost)
    {
        $cost->delete();
        return response()->json(null, 204);
    }

    /**
     * Menyetujui sebuah pengeluaran.
     */
    public function approve(Cost $cost)
    {
        // Hanya biaya 'Realisasi' yang bisa disetujui
        if (!in_array(auth()->user()->role, ['admin', 'finance'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $cost->status = 'Approved';
        $cost->save();

        return response()->json($cost);
    }
}

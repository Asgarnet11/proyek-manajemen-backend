<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\QualityCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class QualityCheckController extends Controller
{
    /**
     * Menampilkan daftar pemeriksaan mutu untuk sebuah proyek.
     */
    public function index(Project $project)
    {
        return $project->qualityChecks()->with('approver:id,name')->latest()->get();
    }

    /**
     * Menyimpan data pemeriksaan mutu baru.
     */
    public function store(Request $request, Project $project)
    {
        if (!in_array(auth()->user()->role, ['admin', 'manajer_proyek', 'QA_QC'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'inspection_area' => 'required|string|max:255',
            'specifications' => 'required|string',
            'status' => 'required|in:Lulus,Gagal,Perlu Perbaikan',
            'remarks' => 'nullable|string',
            'inspection_date' => 'required|date',
            'photo' => 'nullable|image|max:2048', // max 2MB
        ]);

        $data = $validated;

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('quality-photos', 'public');
        }

        $qualityCheck = $project->qualityChecks()->create($data);

        return response()->json($qualityCheck, 201);
    }

    /**
     * Menampilkan detail satu pemeriksaan mutu.
     */
    public function show(QualityCheck $qualityCheck)
    {
        return $qualityCheck->load('approver:id,name', 'project:id,name');
    }

    /**
     * Mengupdate data pemeriksaan mutu.
     */
    public function update(Request $request, QualityCheck $qualityCheck)
    {
        if (!in_array(auth()->user()->role, ['admin', 'manajer_proyek', 'QA_QC'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'inspection_area' => 'sometimes|required|string|max:255',
            'specifications' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:Lulus,Gagal,Perlu Perbaikan',
            'remarks' => 'nullable|string',
            'inspection_date' => 'sometimes|required|date',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($qualityCheck->photo_path) {
                Storage::disk('public')->delete($qualityCheck->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('quality-photos', 'public');
        }

        $qualityCheck->update($validated);
        return response()->json($qualityCheck);
    }

    /**
     * Menyetujui sebuah item pemeriksaan mutu.
     */
    public function approve(QualityCheck $qualityCheck)
    {
        if (!in_array(auth()->user()->role, ['admin', 'QA_QC'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        if ($qualityCheck->approved_by) {
            return response()->json(['message' => 'Data ini sudah disetujui.'], 400);
        }

        $qualityCheck->approved_by = Auth::id();
        $qualityCheck->save();

        return response()->json($qualityCheck->load('approver:id,name'));
    }

    /**
     * Menghapus data pemeriksaan mutu.
     */
    public function destroy(QualityCheck $qualityCheck)
    {
        if (!in_array(auth()->user()->role, ['admin', 'manajer_proyek'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        // Hapus foto dari storage jika ada
        if ($qualityCheck->photo_path) {
            Storage::disk('public')->delete($qualityCheck->photo_path);
        }

        $qualityCheck->delete();
        return response()->json(null, 204);
    }
}

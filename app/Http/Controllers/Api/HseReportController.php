<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\HseReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HseReportController extends Controller
{
    public function index(Project $project)
    {
        return $project->hseReports()->with('reporter:id,name')->latest()->get();
    }

    public function store(Request $request, Project $project)
    {
        if (!in_array(auth()->user()->role, ['HSE Officer', 'Supervisor', 'Manajer Proyek'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'report_type' => 'required|in:Inspeksi Rutin,Laporan Kecelakaan,Laporan Pelanggaran',
            'description' => 'required|string',
            'findings' => 'nullable|string',
            'corrective_action' => 'nullable|string',
            'report_date' => 'required|date',
        ]);

        $hseReport = $project->hseReports()->create($validated + [
            'reported_by' => Auth::id(),
        ]);

        return response()->json($hseReport, 201);
    }

    public function show(HseReport $hseReport)
    {
        return $hseReport->load('reporter:id,name', 'project:id,name');
    }

    public function update(Request $request, HseReport $hseReport)
    {
        if (!in_array(auth()->user()->role, ['HSE Officer', 'Supervisor', 'Manajer Proyek'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'report_type' => 'sometimes|required|in:Inspeksi Rutin,Laporan Kecelakaan,Laporan Pelanggaran',
            'description' => 'sometimes|required|string',
            'findings' => 'nullable|string',
            'corrective_action' => 'nullable|string',
            'report_date' => 'sometimes|required|date',
        ]);

        $hseReport->update($validated);
        return response()->json($hseReport);
    }

    public function destroy(HseReport $hseReport)
    {
        if (!in_array(auth()->user()->role, ['HSE Officer', 'Supervisor', 'Manajer Proyek'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $hseReport->delete();
        return response()->json(null, 204);
    }
}

<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CalendarEventController;
use App\Http\Controllers\Api\CostController;
use App\Http\Controllers\Api\HseReportController;
use App\Http\Controllers\Api\ProgressUpdateController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectDocumentController;
use App\Http\Controllers\Api\QualityCheckController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // === GROUP ROUTE UNTUK PROJECTS ===
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);


    // Hanya untuk admin dan manajer proyek
    Route::middleware('check.role:admin,manajer_proyek')->group(function () {
        Route::post('/projects', [ProjectController::class, 'store']);
        Route::put('/projects/{project}', [ProjectController::class, 'update']);
        Route::patch('/projects/{project}', [ProjectController::class, 'update']);
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);
    });

    Route::middleware('check.role:admin,manajer_proyek,supervisor')->group(function () {
        Route::apiResource('projects.tasks', TaskController::class)->shallow();
    });

    Route::middleware(['auth:sanctum', 'check.role:admin,finance,manajer_proyek'])->group(function () {
        Route::apiResource('projects.costs', CostController::class)->shallow();
        Route::post('/costs/{cost}/approve', [CostController::class, 'approve']);
    });

    Route::middleware(['auth:sanctum', 'check.role:admin,manajer_proyek,QA_QC'])->group(function () {
        Route::apiResource('projects.quality-checks', QualityCheckController::class)->shallow();
        Route::post('/quality-checks/{qualityCheck}/approve', [QualityCheckController::class, 'approve']);
    });

    Route::middleware('role:hse_officer,supervisor,manajer_proyek')->group(function () {
        Route::apiResource('projects.hse-reports', HseReportController::class)->shallow();
    });

    Route::apiResource('projects.calendar-events', CalendarEventController::class)->shallow();
    // 
    Route::get('/projects/{project}/documents', [ProjectDocumentController::class, 'index']);
    Route::post('/projects/{project}/documents', [ProjectDocumentController::class, 'store']);

    Route::middleware('role:admin,supervisor')->group(function () {
        Route::delete('/documents/{projectDocument}', [ProjectDocumentController::class, 'destroy']);
    });

    // === GROUP ROUTE UNTUK PROGRESS UPDATE ===
    Route::middleware('role:"manajer_proyek",supervisor,client')->group(function () {
        Route::get('/projects/{project}/progress-updates', [ProgressUpdateController::class, 'index']);
        Route::get('/progress-updates/{progress_update}', [ProgressUpdateController::class, 'show']);
    });

    Route::middleware('role:"manajer_proyek",supervisor')->group(function () {
        Route::post('/projects/{project}/progress-updates', [ProgressUpdateController::class, 'store']);
        Route::delete('/progress-updates/{progress_update}', [ProgressUpdateController::class, 'destroy']);
    });
});

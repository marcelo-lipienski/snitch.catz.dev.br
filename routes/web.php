<?php

use App\Http\Controllers\RepositoryController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

use App\Models\Report;

Route::get('/', function () {
    $latestReports = Report::where('status', 'completed')
        ->latest()
        ->take(5)
        ->get();

    return view('welcome', compact('latestReports'));
});

Route::post('/analyze', [RepositoryController::class, 'analyze']);
Route::get('/report/preview/technical', [ReportController::class, 'previewTechnical'])->name('report.preview.technical');
Route::get('/report/preview/business', [ReportController::class, 'previewBusiness'])->name('report.preview.business');
Route::get('/report/{uuid}', [ReportController::class, 'show'])->name('report.show');
Route::get('/report/{uuid}/business', [ReportController::class, 'business'])->name('report.business');
Route::get('/report/{uuid}/status', [ReportController::class, 'status'])->name('report.status');

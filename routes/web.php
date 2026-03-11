<?php

use App\Http\Controllers\RepositoryController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/analyze', [RepositoryController::class, 'analyze']);
Route::get('/report/{uuid}', [ReportController::class, 'show'])->name('report.show');

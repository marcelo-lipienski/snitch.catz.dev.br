<?php

use App\Http\Controllers\RepositoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/analyze', [RepositoryController::class, 'analyze']);

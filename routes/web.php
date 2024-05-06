<?php

use App\Http\Controllers\ConvertController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home');
Route::view('/docs', 'docs');

Route::post('run', [ConvertController::class, 'run']);

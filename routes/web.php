<?php

use App\Http\Controllers\ConvertController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('run', [ConvertController::class, 'run']);

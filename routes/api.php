<?php

use App\Http\Controllers\ConvertController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/convert', [ConvertController::class, 'run']); //->middleware('auth:sanctum');

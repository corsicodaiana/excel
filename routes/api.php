<?php

use App\Http\Controllers\PruebaController;
use Illuminate\Support\Facades\Route;

Route::post('/prueba', [PruebaController::class, 'index']);

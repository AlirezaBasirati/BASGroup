<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;

Route::post('/message', [MessageController::class, 'store']);
Route::post('/message/read', [MessageController::class, 'read']);

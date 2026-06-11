<?php

declare(strict_types=1);

use App\Http\Controllers\HistoryController;
use App\Http\Controllers\SendController;
use Illuminate\Support\Facades\Route;

Route::post('/send', [SendController::class, 'send']);

Route::get('/history/{id}', [HistoryController::class, 'get']);

<?php

use App\Http\Controllers\Telegram\IncomingData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/teleApi', [IncomingData::class, 'RedirectByUrl']);

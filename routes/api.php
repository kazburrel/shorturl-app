<?php

use App\Http\Controllers\UrlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/encode', [UrlController::class, 'encode'])->name('url.encode');
Route::post('/decode', [UrlController::class, 'decode'])->name('url.decode');

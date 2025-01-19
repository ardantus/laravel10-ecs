<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FolderController;

// Rute dasar untuk testing
Route::get('/', function () {
    return view('welcome');
});

// Rute login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute dashboard dan folder (dilindungi middleware auth)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/folders', [FolderController::class, 'index'])->name('folders');
    Route::post('/folders', [FolderController::class, 'createFolder']);
    Route::post('/folders/{folder}/upload', [FolderController::class, 'uploadFile']);
    Route::delete('/files/{file}', [FolderController::class, 'deleteFile']);
    Route::delete('/folders/{folder}', [FolderController::class, 'deleteFolder'])->name('folders.delete');
});

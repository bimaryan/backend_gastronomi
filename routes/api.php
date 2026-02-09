<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactDetailController;
use App\Http\Controllers\ContactItemController;
use App\Http\Controllers\EventSliderController;
use App\Http\Controllers\FooterKontakController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KelasPesertaController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\TiketKategoriController;
use App\Http\Controllers\TentangKamiController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PartnerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. PUBLIC ROUTES (Bisa diakses tanpa login)
// ==========================================

// Auth
Route::post('/login', [AuthController::class, 'login']);

// Data Public (Frontend Guest)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

Route::get('/kelas', [KelasController::class, 'index']);
Route::get('/kelas/{id}', [KelasController::class, 'show']);

Route::get('/tiket-kategoris', [TiketKategoriController::class, 'index']);
Route::get('/tiket-kategoris/{id}', [TiketKategoriController::class, 'show']);

Route::get('/contact-items', [ContactItemController::class, 'index']);
Route::get('/contact-details', [ContactDetailController::class, 'index']);

Route::get('/tentang-kami', [TentangKamiController::class, 'index']);
Route::get('/layanan', [LayananController::class, 'index']);
Route::get('/partner', [PartnerController::class, 'index']);
Route::get('/footer', [FooterKontakController::class, 'index']);
Route::get('/kontak-hero', [KontakController::class, 'index']);

Route::get('/sliders', [EventSliderController::class, 'index']); // Jika ada

// ==========================================
// 2. PROTECTED ROUTES (Harus Login / Punya Token)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {

    // User & Session
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);

    // --- MANAJEMEN KATEGORI ---
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // --- MANAJEMEN KELAS ---
    Route::post('/kelas', [KelasController::class, 'store']);
    Route::post('/kelas/{id}', [KelasController::class, 'update']); // Pakai POST untuk update file gambar
    Route::delete('/kelas/{id}', [KelasController::class, 'destroy']);

    // --- MANAJEMEN TIKET ---
    Route::post('/tiket-kategoris', [TiketKategoriController::class, 'store']);
    Route::put('/tiket-kategoris/{id}', [TiketKategoriController::class, 'update']);
    Route::delete('/tiket-kategoris/{id}', [TiketKategoriController::class, 'destroy']);

    // --- MANAJEMEN PESERTA ---
    // Note: Store peserta mungkin perlu public jika user daftar sendiri,
    // tapi jika admin yang input, taruh di sini.
    Route::get('/peserta', [KelasPesertaController::class, 'index']);
    Route::post('/peserta', [KelasPesertaController::class, 'store']);
    Route::get('/peserta/{id}', [KelasPesertaController::class, 'show']);
    Route::put('/peserta/{id}', [KelasPesertaController::class, 'update']);
    Route::delete('/peserta/{id}', [KelasPesertaController::class, 'destroy']);

    // --- MANAJEMEN KONTAK ---
    Route::post('/contact-items', [ContactItemController::class, 'store']);
    Route::put('/contact-items/{id}', [ContactItemController::class, 'update']);
    Route::delete('/contact-items/{id}', [ContactItemController::class, 'destroy']);

    Route::post('/contact-details', [ContactDetailController::class, 'store']);
    Route::put('/contact-details/{id}', [ContactDetailController::class, 'update']);
    Route::delete('/contact-details/{id}', [ContactDetailController::class, 'destroy']);

    // --- PENGATURAN HALAMAN (Footer & Hero) ---
    Route::post('/tentang-kami', [TentangKamiController::class, 'update']);
    Route::post('/layanan', [LayananController::class, 'update']);
    Route::post('/partner', [PartnerController::class, 'update']);
    Route::post('/footer', [FooterKontakController::class, 'update']);
    Route::post('/kontak-hero', [KontakController::class, 'update']);

    // --- SLIDER ---
    Route::post('/sliders', [EventSliderController::class, 'store']);
    Route::post('/sliders/{id}', [EventSliderController::class, 'update']);
    Route::delete('/sliders/{id}', [EventSliderController::class, 'destroy']);
});

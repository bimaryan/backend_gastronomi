<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kontak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KontakController extends Controller
{
    /**
     * GET /api/kontak-hero
     * Mengambil data Hero Section halaman kontak.
     */
    public function index()
    {
        // Ambil data pertama
        $kontak = Kontak::first();

        if (! $kontak) {
            return response()->json([
                'success' => true,
                'message' => 'Belum ada konfigurasi Hero Kontak',
                'data' => null,
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Hero Kontak',
            'data' => $kontak,
        ], 200);
    }

    /**
     * POST /api/kontak-hero
     * Simpan atau Update konfigurasi Hero.
     */
    public function update(Request $request)
    {
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:255',
            'hero_description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Cek apakah data sudah ada
        $kontak = Kontak::first();

        if ($kontak) {
            // Update jika ada
            $kontak->update($request->all());
            $message = 'Hero Kontak berhasil diperbarui';
        } else {
            // Create jika belum ada
            $kontak = Kontak::create($request->all());
            $message = 'Hero Kontak berhasil dibuat';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $kontak,
        ], 200);
    }
}

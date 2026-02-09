<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FooterKontak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FooterKontakController extends Controller
{
    /**
     * GET /api/footer
     * Mengambil data footer.
     * Jika belum ada data di database, return data kosong atau null.
     */
    public function index()
    {
        // Ambil baris pertama saja, karena footer bersifat global/single
        $footer = FooterKontak::first();

        if (! $footer) {
            return response()->json([
                'success' => true,
                'message' => 'Belum ada konfigurasi footer',
                'data' => null,
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Footer',
            'data' => $footer,
        ], 200);
    }

    /**
     * POST /api/footer
     * Menyimpan atau Mengupdate data footer.
     * Logikanya: Jika data sudah ada -> Update. Jika belum -> Create.
     */
    public function update(Request $request)
    {
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:100',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'copyright_text' => 'required|string|max:255',
            'social_facebook' => 'nullable|url', // Validasi format URL
            'social_instagram' => 'nullable|url',
            'social_twitter' => 'nullable|url',
            'social_youtube' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Update or Create
        // Kita gunakan updateOrCreate dengan kondisi id=1 (atau ambil first)
        // Ini memastikan hanya ada 1 row di tabel ini.

        $footer = FooterKontak::first();

        if ($footer) {
            // Jika sudah ada, update
            $footer->update($request->all());
            $message = 'Footer berhasil diperbarui';
        } else {
            // Jika belum ada, create baru
            $footer = FooterKontak::create($request->all());
            $message = 'Footer berhasil dibuat';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $footer,
        ], 200);
    }
}

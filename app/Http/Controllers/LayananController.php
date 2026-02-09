<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    // GET: Ambil semua data dan format jadi JSON object { "key": "value" }
    public function index()
    {
        $data = Layanan::all();
        $formatted = [];

        foreach ($data as $item) {
            $value = $item->content_value;
            // Jika tipe JSON, decode dulu
            if ($item->content_type === 'json') {
                $value = json_decode($value);
            }
            $formatted[$item->section_key] = $value;
        }

        return response()->json($formatted, 200);
    }

    // POST: Simpan atau Update data
    public function update(Request $request)
    {
        $inputs = $request->all();

        foreach ($inputs as $key => $value) {
            $type = 'text';
            $valToSave = $value;

            // Cek jika data berupa array/object, simpan sebagai JSON
            if (is_array($value) || is_object($value)) {
                $type = 'json';
                $valToSave = json_encode($value);
            }

            Layanan::updateOrCreate(
                ['section_key' => $key], // Cari berdasarkan key
                [
                    'section' => 'main',
                    'content_type' => $type,
                    'content_value' => $valToSave,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Layanan berhasil diperbarui',
        ], 200);
    }
}

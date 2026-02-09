<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TentangKami;
use Illuminate\Http\Request;

class TentangKamiController extends Controller
{
    public function index()
    {
        try {
            $data = TentangKami::all();
            $formatted = [];

            foreach ($data as $item) {
                $value = $item->content_value;
                if ($item->content_type === 'json') {
                    $value = json_decode($value);
                }
                $formatted[$item->section_key] = $value;
            }

            return response()->json($formatted, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $inputs = $request->all();

            foreach ($inputs as $key => $value) {
                $type = 'text';
                $valToSave = $value;

                if (is_array($value) || is_object($value)) {
                    $type = 'json';
                    // Tambahkan JSON_UNESCAPED_UNICODE agar karakter spesial aman
                    $valToSave = json_encode($value, JSON_UNESCAPED_UNICODE);
                }

                TentangKami::updateOrCreate(
                    ['section_key' => $key],
                    [
                        'section' => 'main',
                        'content_type' => $type,
                        'content_value' => $valToSave,
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Data Tentang Kami berhasil diperbarui',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan: '.$e->getMessage(),
            ], 500);
        }
    }
}

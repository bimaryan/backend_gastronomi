<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index()
    {
        $data = Partner::all();
        $formatted = [];

        foreach ($data as $item) {
            $value = $item->content_value;
            if ($item->content_type === 'json') {
                $value = json_decode($value);
            }
            $formatted[$item->section_key] = $value;
        }

        return response()->json($formatted, 200);
    }

    public function update(Request $request)
    {
        $inputs = $request->all();

        foreach ($inputs as $key => $value) {
            $type = 'text';
            $valToSave = $value;

            if (is_array($value) || is_object($value)) {
                $type = 'json';
                $valToSave = json_encode($value);
            }

            Partner::updateOrCreate(
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
            'message' => 'Data Partner berhasil diperbarui',
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EventSlider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EventSliderController extends Controller
{
    /**
     * GET /api/sliders
     * Menampilkan semua slider (urutan berdasarkan order_position)
     */
    public function index(Request $request)
    {
        $query = EventSlider::orderBy('order_position', 'asc');

        // Filter untuk hanya menampilkan yang aktif (Frontend biasanya pakai ini)
        if ($request->has('active')) {
            $query->where('is_active', true);
        }

        $sliders = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'List Event Sliders',
            'data' => $sliders,
        ], 200);
    }

    /**
     * POST /api/sliders
     * Upload gambar baru
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // Max 5MB
            'description' => 'nullable|string',
            'order_position' => 'integer',
            'is_active' => 'boolean',
            'crop_mode' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Proses File Gambar
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Ambil Metadata Gambar (Width, Height, Orientation)
            $imageInfo = getimagesize($file);
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $orientation = ($width > $height) ? 'landscape' : (($width < $height) ? 'portrait' : 'square');

            // Simpan file
            $originalName = $file->getClientOriginalName();
            $path = $file->store('sliders', 'public');

            // 3. Simpan ke Database
            $slider = EventSlider::create([
                'filename' => $path,
                'original_name' => $originalName,
                'description' => $request->description,
                'order_position' => $request->order_position ?? 0,
                'is_active' => $request->is_active ?? 1,
                'orientation' => $orientation,
                'image_width' => $width,
                'image_height' => $height,
                'crop_mode' => $request->crop_mode ?? 'smart',
                'processed' => 1, // Kita anggap langsung processed
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Slider berhasil diupload',
                'data' => $slider,
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'File gambar wajib diupload'], 400);
    }

    /**
     * GET /api/sliders/{id}
     */
    public function show($id)
    {
        $slider = EventSlider::find($id);

        if (! $slider) {
            return response()->json(['success' => false, 'message' => 'Slider tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Slider',
            'data' => $slider,
        ], 200);
    }

    /**
     * POST/PUT /api/sliders/{id}
     * Update slider (Gunakan POST dengan _method=PUT di Postman jika upload file)
     */
    public function update(Request $request, $id)
    {
        $slider = EventSlider::find($id);

        if (! $slider) {
            return response()->json(['success' => false, 'message' => 'Slider tidak ditemukan'], 404);
        }

        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'description' => 'nullable|string',
            'order_position' => 'integer',
            'is_active' => 'boolean',
            'crop_mode' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Cek apakah ada file baru yang diupload
        if ($request->hasFile('image')) {
            // Hapus file lama
            if ($slider->filename && Storage::disk('public')->exists($slider->filename)) {
                Storage::disk('public')->delete($slider->filename);
            }

            // Proses file baru
            $file = $request->file('image');
            $imageInfo = getimagesize($file);
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $orientation = ($width > $height) ? 'landscape' : (($width < $height) ? 'portrait' : 'square');

            $slider->filename = $file->store('sliders', 'public');
            $slider->original_name = $file->getClientOriginalName();
            $slider->image_width = $width;
            $slider->image_height = $height;
            $slider->orientation = $orientation;
        }

        // 3. Update data text
        $slider->description = $request->description ?? $slider->description;
        $slider->order_position = $request->order_position ?? $slider->order_position;
        $slider->is_active = $request->has('is_active') ? $request->is_active : $slider->is_active;
        $slider->crop_mode = $request->crop_mode ?? $slider->crop_mode;

        $slider->save();

        return response()->json([
            'success' => true,
            'message' => 'Slider berhasil diupdate',
            'data' => $slider,
        ], 200);
    }

    /**
     * DELETE /api/sliders/{id}
     */
    public function destroy($id)
    {
        $slider = EventSlider::find($id);

        if (! $slider) {
            return response()->json(['success' => false, 'message' => 'Slider tidak ditemukan'], 404);
        }

        // Hapus fisik file
        if ($slider->filename && Storage::disk('public')->exists($slider->filename)) {
            Storage::disk('public')->delete($slider->filename);
        }

        $slider->delete();

        return response()->json([
            'success' => true,
            'message' => 'Slider berhasil dihapus',
        ], 200);
    }
}

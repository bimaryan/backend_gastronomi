<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories;

class CategoryController extends Controller
{
    /**
     * GET /api/categories
     * Menampilkan semua kategori
     */
    public function index()
    {
        $categories = Categories::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar semua kategori',
            'data'    => $categories
        ], 200);
    }

    /**
     * POST /api/categories
     * Membuat kategori baru
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'nama' => 'required|string|max:100|unique:categories,nama',
        ]);

        // 2. Simpan Data
        $category = Categories::create([
            'nama' => $request->nama,
        ]);

        // 3. Response
        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dibuat',
            'data'    => $category
        ], 201);
    }

    /**
     * GET /api/categories/{id}
     * Menampilkan detail satu kategori
     */
    public function show($id)
    {
        $category = Categories::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail kategori',
            'data'    => $category
        ], 200);
    }

    /**
     * PUT/PATCH /api/categories/{id}
     * Update kategori
     */
    public function update(Request $request, $id)
    {
        $category = Categories::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        }

        // 1. Validasi
        // Note: unique:categories,nama,$id artinya cek unik TAPI abaikan id yang sedang diedit
        $request->validate([
            'nama' => 'required|string|max:100|unique:categories,nama,' . $id,
        ]);

        // 2. Update Data
        $category->update([
            'nama' => $request->nama,
        ]);

        // 3. Response
        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diupdate',
            'data'    => $category
        ], 200);
    }

    /**
     * DELETE /api/categories/{id}
     * Hapus kategori
     */
    public function destroy($id)
    {
        $category = Categories::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
            ], 404);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus',
        ], 200);
    }
}

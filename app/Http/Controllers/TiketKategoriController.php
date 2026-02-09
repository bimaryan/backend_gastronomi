<?php

namespace App\Http\Controllers;

use App\Models\TiketKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TiketKategoriController extends Controller
{
    /**
     * GET /api/tiket-kategoris
     * Bisa filter berdasarkan kelas_id: /api/tiket-kategoris?kelas_id=1
     */
    public function index(Request $request)
    {
        $query = TiketKategori::query();

        // Filter jika ada parameter kelas_id
        if ($request->has('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter hanya yang aktif (opsional, jika ingin menampilkan semua hapus baris ini)
        // $query->where('is_active', true);

        $tiket = $query->with('kelas')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar Kategori Tiket',
            'data' => $tiket,
        ], 200);
    }

    /**
     * POST /api/tiket-kategoris
     * Membuat tiket baru
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'kelas_id' => 'required|exists:kelas,id', // Pastikan ID kelas valid
            'nama_kategori' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'manfaat' => 'required', // Bisa string atau array (JSON)
            'is_populer' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Simpan Data
        $tiket = TiketKategori::create([
            'kelas_id' => $request->kelas_id,
            'nama_kategori' => $request->nama_kategori,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'link' => $request->link,
            'manfaat' => $request->manfaat, // Model akan otomatis meng-cast ini jika diset array
            'is_populer' => $request->is_populer ?? 0,
            'is_active' => $request->is_active ?? 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori Tiket berhasil dibuat',
            'data' => $tiket,
        ], 201);
    }

    /**
     * GET /api/tiket-kategoris/{id}
     * Detail satu tiket
     */
    public function show($id)
    {
        $tiket = TiketKategori::with('kelas')->find($id);

        if (! $tiket) {
            return response()->json(['success' => false, 'message' => 'Tiket tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Kategori Tiket',
            'data' => $tiket,
        ], 200);
    }

    /**
     * PUT /api/tiket-kategoris/{id}
     * Update tiket
     */
    public function update(Request $request, $id)
    {
        $tiket = TiketKategori::find($id);

        if (! $tiket) {
            return response()->json(['success' => false, 'message' => 'Tiket tidak ditemukan'], 404);
        }

        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'kelas_id' => 'nullable|exists:kelas,id',
            'nama_kategori' => 'string|max:100',
            'harga' => 'numeric|min:0',
            'is_populer' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Update Data
        $tiket->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kategori Tiket berhasil diupdate',
            'data' => $tiket,
        ], 200);
    }

    /**
     * DELETE /api/tiket-kategoris/{id}
     * Hapus tiket
     */
    public function destroy($id)
    {
        $tiket = TiketKategori::find($id);

        if (! $tiket) {
            return response()->json(['success' => false, 'message' => 'Tiket tidak ditemukan'], 404);
        }

        $tiket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori Tiket berhasil dihapus',
        ], 200);
    }
}

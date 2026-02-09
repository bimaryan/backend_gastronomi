<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KelasPeserta;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KelasPesertaController extends Controller
{
    /**
     * GET /api/peserta
     * Filter: ?kelas_id=1 (Untuk melihat peserta di kelas tertentu)
     */
    public function index(Request $request)
    {
        $query = KelasPeserta::with(['kelas', 'tiketKategori']);

        // Filter berdasarkan Kelas ID
        if ($request->has('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter berdasarkan Status Pembayaran
        if ($request->has('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        $peserta = $query->latest('tanggal_daftar')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar Peserta Kelas',
            'data'    => $peserta
        ], 200);
    }

    /**
     * POST /api/peserta
     * Mendaftarkan peserta baru
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'kelas_id'          => 'required|exists:kelas,id',
            'tiket_kategori_id' => 'nullable|exists:tiket_kategoris,id',
            'nama_peserta'      => 'required|string|max:255',
            'email'             => 'nullable|email|max:255',
            'no_hp'             => 'nullable|string|max:20',
            'status_pembayaran' => ['required', Rule::in(['Lunas', 'Belum Lunas', 'Cicilan'])],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Simpan Data
        $peserta = KelasPeserta::create([
            'kelas_id'          => $request->kelas_id,
            'tiket_kategori_id' => $request->tiket_kategori_id,
            'nama_peserta'      => $request->nama_peserta,
            'email'             => $request->email,
            'no_hp'             => $request->no_hp,
            'status_pembayaran' => $request->status_pembayaran,
            // tanggal_daftar otomatis terisi current timestamp dari database
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Peserta berhasil didaftarkan',
            'data'    => $peserta
        ], 201);
    }

    /**
     * GET /api/peserta/{id}
     * Detail satu peserta
     */
    public function show($id)
    {
        $peserta = KelasPeserta::with(['kelas', 'tiketKategori'])->find($id);

        if (!$peserta) {
            return response()->json(['success' => false, 'message' => 'Peserta tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Peserta',
            'data'    => $peserta
        ], 200);
    }

    /**
     * PUT /api/peserta/{id}
     * Update data peserta (misal: update status pembayaran)
     */
    public function update(Request $request, $id)
    {
        $peserta = KelasPeserta::find($id);

        if (!$peserta) {
            return response()->json(['success' => false, 'message' => 'Peserta tidak ditemukan'], 404);
        }

        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'nama_peserta'      => 'string|max:255',
            'email'             => 'nullable|email',
            'no_hp'             => 'nullable|string|max:20',
            'status_pembayaran' => [Rule::in(['Lunas', 'Belum Lunas', 'Cicilan'])],
            'tiket_kategori_id' => 'nullable|exists:tiket_kategoris,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Update
        $peserta->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data peserta berhasil diupdate',
            'data'    => $peserta
        ], 200);
    }

    /**
     * DELETE /api/peserta/{id}
     * Hapus peserta
     */
    public function destroy($id)
    {
        $peserta = KelasPeserta::find($id);

        if (!$peserta) {
            return response()->json(['success' => false, 'message' => 'Peserta tidak ditemukan'], 404);
        }

        $peserta->delete();

        return response()->json([
            'success' => true,
            'message' => 'Peserta berhasil dihapus',
        ], 200);
    }
}

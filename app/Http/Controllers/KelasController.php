<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class KelasController extends Controller
{
    /**
     * GET /api/kelas
     * Menampilkan semua kelas
     */
    public function index()
    {
        // Mengambil data kelas beserta relasi kategorinya
        // Mengurutkan dari yang terbaru
        $kelas = Kelas::with('kategori')->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar Kelas',
            'data' => $kelas,
        ], 200);
    }

    /**
     * POST /api/kelas
     * Menambah kelas baru (termasuk upload foto)
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'nama_kelas' => 'required|string|max:255',
            'kategori_id' => 'nullable|exists:categories,id', // Pastikan kategori ada
            'deskripsi' => 'nullable|string',
            'jadwal' => 'nullable|string',
            'ruangan' => 'nullable|string|max:100',
            'biaya' => 'nullable|numeric|min:0',
            'metode_pembayaran' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'gambaran_event' => 'nullable', // Bisa array atau JSON string
            'total_peserta' => 'integer|min:0',
            'link_navigasi' => 'nullable|string|max:500',
            'is_link_eksternal' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Handle Upload Foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            // Simpan di folder: storage/app/public/kelas
            $fotoPath = $request->file('foto')->store('kelas', 'public');
        }

        // 3. Simpan Data
        $kelas = Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'kategori_id' => $request->kategori_id,
            'deskripsi' => $request->deskripsi,
            'jadwal' => $request->jadwal,
            'ruangan' => $request->ruangan,
            'biaya' => $request->biaya,
            'metode_pembayaran' => $request->metode_pembayaran,
            'foto' => $fotoPath, // Path file disimpan
            'gambaran_event' => $request->gambaran_event, // Otomatis jadi JSON jika di-cast di Model
            'total_peserta' => $request->total_peserta ?? 0,
            'link_navigasi' => $request->link_navigasi ?? '',
            'is_link_eksternal' => $request->is_link_eksternal ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil ditambahkan',
            'data' => $kelas,
        ], 201);
    }

    /**
     * GET /api/kelas/{id}
     * Detail satu kelas
     */
    public function show($id)
    {
        $kelas = Kelas::with('kategori')->find($id);

        if (! $kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Kelas',
            'data' => $kelas,
        ], 200);
    }

    /**
     * POST/PUT /api/kelas/{id}
     * Update kelas (Gunakan POST dengan _method=PUT jika mengirim file via Postman)
     */
    public function update(Request $request, $id)
    {
        $kelas = Kelas::find($id);

        if (! $kelas) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan'], 404);
        }

        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'nama_kelas' => 'required|string|max:255',
            'kategori_id' => 'nullable|exists:categories,id',
            'biaya' => 'nullable|numeric',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_link_eksternal' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Handle Update Foto
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($kelas->foto && Storage::disk('public')->exists($kelas->foto)) {
                Storage::disk('public')->delete($kelas->foto);
            }
            // Upload foto baru
            $kelas->foto = $request->file('foto')->store('kelas', 'public');
        }

        // 3. Update Data Lainnya
        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'kategori_id' => $request->kategori_id,
            'deskripsi' => $request->deskripsi ?? $kelas->deskripsi,
            'jadwal' => $request->jadwal ?? $kelas->jadwal,
            'ruangan' => $request->ruangan ?? $kelas->ruangan,
            'biaya' => $request->biaya ?? $kelas->biaya,
            'metode_pembayaran' => $request->metode_pembayaran ?? $kelas->metode_pembayaran,
            'gambaran_event' => $request->gambaran_event ?? $kelas->gambaran_event,
            'total_peserta' => $request->total_peserta ?? $kelas->total_peserta,
            'link_navigasi' => $request->link_navigasi ?? $kelas->link_navigasi,
            'is_link_eksternal' => $request->is_link_eksternal ?? $kelas->is_link_eksternal,
            // Foto sudah dihandle di atas
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil diupdate',
            'data' => $kelas,
        ], 200);
    }

    /**
     * DELETE /api/kelas/{id}
     * Hapus kelas
     */
    public function destroy($id)
    {
        $kelas = Kelas::find($id);

        if (! $kelas) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan'], 404);
        }

        // Hapus foto dari storage jika ada
        if ($kelas->foto && Storage::disk('public')->exists($kelas->foto)) {
            Storage::disk('public')->delete($kelas->foto);
        }

        $kelas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil dihapus',
        ], 200);
    }
}

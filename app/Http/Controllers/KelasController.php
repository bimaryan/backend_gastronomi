<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator; // Tambahkan ini untuk cek file manual

class KelasController extends Controller
{
    /**
     * GET /api/kelas
     */
    public function index()
    {
        $kelas = Kelas::with('kategori')->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar Kelas',
            'data' => $kelas,
        ], 200);
    }

    /**
     * POST /api/kelas
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'nama_kelas' => 'required|string|max:255',
            'kategori_id' => 'nullable|exists:categories,id',
            'deskripsi' => 'nullable|string',
            'jadwal' => 'nullable|string',
            'ruangan' => 'nullable|string|max:100',
            'biaya' => 'nullable|numeric|min:0',
            'metode_pembayaran' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gambaran_event' => 'nullable',
            'total_peserta' => 'integer|min:0',
            'link_navigasi' => 'nullable|string|max:500',
            'is_link_eksternal' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Handle Upload Foto (LANGSUNG KE PUBLIC)
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');

            // Bikin nama file unik (time + nama asli)
            $filename = time().'_'.$file->getClientOriginalName();

            // Tentukan folder tujuan di dalam folder public
            // Ini akan otomatis buat folder 'uploads/kelas' di dalam public jika belum ada
            $file->move(public_path('uploads/kelas'), $filename);

            // Simpan path relatif untuk database
            $fotoPath = 'uploads/kelas/'.$filename;
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
            'foto' => $fotoPath, // Path contoh: uploads/kelas/12345_gambar.jpg
            'gambaran_event' => $request->gambaran_event,
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
     */
    public function show($id)
    {
        $kelas = Kelas::with('kategori')->find($id);

        if (! $kelas) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Kelas',
            'data' => $kelas,
        ], 200);
    }

    /**
     * POST/PUT /api/kelas/{id}
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
            // Hapus foto lama jika ada di folder public
            if ($kelas->foto && file_exists(public_path($kelas->foto))) {
                unlink(public_path($kelas->foto));
            }

            // Upload foto baru
            $file = $request->file('foto');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/kelas'), $filename);

            // Update path di database
            $kelas->foto = 'uploads/kelas/'.$filename;
        }

        // 3. Update Data Lainnya
        // Catatan: Jika foto tidak diupload, $kelas->foto tidak berubah
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
            // Field foto sudah dihandle manual di atas, jadi tidak perlu dimasukkan ke array update ini jika tidak ingin ditimpa null
        ]);

        // Simpan perubahan foto jika ada (karena update() mass assignment mungkin mengabaikan properti yang diset manual sebelumnya)
        $kelas->save();

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil diupdate',
            'data' => $kelas,
        ], 200);
    }

    /**
     * DELETE /api/kelas/{id}
     */
    public function destroy($id)
    {
        $kelas = Kelas::find($id);

        if (! $kelas) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan'], 404);
        }

        // Hapus foto dari folder public jika ada
        if ($kelas->foto && file_exists(public_path($kelas->foto))) {
            unlink(public_path($kelas->foto));
        }

        $kelas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil dihapus',
        ], 200);
    }
}

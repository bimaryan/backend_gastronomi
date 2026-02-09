<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ContactDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactDetailController extends Controller
{
    /**
     * GET /api/contact-details
     * Bisa filter by item: /api/contact-details?contact_item_id=1
     */
    public function index(Request $request)
    {
        $query = ContactDetail::query();

        // Filter berdasarkan Parent (Contact Item)
        if ($request->has('contact_item_id')) {
            $query->where('contact_item_id', $request->contact_item_id);
        }

        // Urutkan berdasarkan detail_order (asc)
        $details = $query->with('contactItem')->orderBy('detail_order', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'List Contact Details',
            'data' => $details,
        ], 200);
    }

    /**
     * POST /api/contact-details
     * Tambah detail baru
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'contact_item_id' => 'required|exists:contact_items,id', // Wajib ada parent-nya
            'detail_text' => 'required|string|max:255',
            'detail_order' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Simpan
        $detail = ContactDetail::create([
            'contact_item_id' => $request->contact_item_id,
            'detail_text' => $request->detail_text,
            'detail_order' => $request->detail_order ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Detail Kontak berhasil ditambahkan',
            'data' => $detail,
        ], 201);
    }

    /**
     * GET /api/contact-details/{id}
     * Lihat satu detail
     */
    public function show($id)
    {
        $detail = ContactDetail::with('contactItem')->find($id);

        if (! $detail) {
            return response()->json(['success' => false, 'message' => 'Detail tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Kontak',
            'data' => $detail,
        ], 200);
    }

    /**
     * PUT /api/contact-details/{id}
     * Update detail
     */
    public function update(Request $request, $id)
    {
        $detail = ContactDetail::find($id);

        if (! $detail) {
            return response()->json(['success' => false, 'message' => 'Detail tidak ditemukan'], 404);
        }

        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'contact_item_id' => 'exists:contact_items,id',
            'detail_text' => 'string|max:255',
            'detail_order' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Update
        $detail->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Detail Kontak berhasil diupdate',
            'data' => $detail,
        ], 200);
    }

    /**
     * DELETE /api/contact-details/{id}
     * Hapus detail
     */
    public function destroy($id)
    {
        $detail = ContactDetail::find($id);

        if (! $detail) {
            return response()->json(['success' => false, 'message' => 'Detail tidak ditemukan'], 404);
        }

        $detail->delete();

        return response()->json([
            'success' => true,
            'message' => 'Detail Kontak berhasil dihapus',
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ContactItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactItemController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactItems::orderBy('order_position', 'asc');

        if ($request->has('only_active')) {
            $query->where('is_active', true);
        }

        $items = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'List Contact Items',
            'data' => $items,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'icon' => 'required|string|max:50',
            'title' => 'required|string|max:100',
            'details' => 'required|array', // Harus array
            'action_url' => 'nullable|string|max:500',
            'order_position' => 'integer',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $item = ContactItems::create([
            'icon' => $request->icon,
            'title' => $request->title,
            'details' => $request->details,
            'action_url' => $request->action_url,
            'order_position' => $request->order_position ?? 0,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contact Item berhasil dibuat',
            'data' => $item,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $item = ContactItems::find($id);

        if (! $item) {
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'icon' => 'string|max:50',
            'title' => 'string|max:100',
            'details' => 'array',
            'action_url' => 'nullable|string|max:500',
            'order_position' => 'integer',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $item->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Contact Item berhasil diupdate',
            'data' => $item,
        ], 200);
    }

    public function destroy($id)
    {
        $item = ContactItems::find($id);

        if (! $item) {
            return response()->json(['success' => false, 'message' => 'Item tidak ditemukan'], 404);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact Item berhasil dihapus',
        ], 200);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::query()->orderBy('nama');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('kode', 'like', '%' . $request->search . '%')
                    ->orWhere('nama', 'like', '%' . $request->search . '%');
            });
        }

        return response()->json($query->paginate((int) $request->input('per_page', 10)));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('barang.create'), 403);

        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:255', 'unique:barang,kode'],
            'nama' => ['required', 'string', 'max:255'],
            'stok' => ['required', 'integer', 'min:0'],
        ]);

        $barang = Barang::create($validated);
        ActivityLogger::log('api.barang.created', $barang, 'API menambahkan data barang', ['after' => $barang->toArray()], $request);

        return response()->json(['message' => 'Barang berhasil dibuat.', 'data' => $barang], 201);
    }

    public function show(Barang $barang)
    {
        return response()->json(['data' => $barang]);
    }

    public function update(Request $request, Barang $barang)
    {
        abort_unless(auth()->user()->hasPermission('barang.update'), 403);

        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:255', 'unique:barang,kode,' . $barang->id],
            'nama' => ['required', 'string', 'max:255'],
            'stok' => ['required', 'integer', 'min:0'],
        ]);

        $before = $barang->toArray();
        $barang->update($validated);
        ActivityLogger::log('api.barang.updated', $barang, 'API memperbarui data barang', ['before' => $before, 'after' => $barang->fresh()->toArray()], $request);

        return response()->json(['message' => 'Barang berhasil diperbarui.', 'data' => $barang]);
    }

    public function destroy(Barang $barang)
    {
        abort_unless(auth()->user()->hasPermission('barang.delete'), 403);

        if ($barang->barangMasuk()->exists() || $barang->barangKeluar()->exists()) {
            return response()->json(['message' => 'Barang tidak bisa dihapus karena sudah memiliki transaksi.'], 422);
        }

        $payload = $barang->toArray();
        $barang->delete();
        ActivityLogger::log('api.barang.deleted', $barang, 'API menghapus data barang', ['before' => $payload]);

        return response()->json(['message' => 'Barang berhasil dihapus.']);
    }
}

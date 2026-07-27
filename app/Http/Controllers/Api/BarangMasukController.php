<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangMasukController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('barang_masuk.view'), 403);

        return response()->json(
            BarangMasuk::with('barang')->latest()->paginate((int) $request->input('per_page', 10))
        );
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('barang_masuk.create'), 403);

        $validated = $request->validate([
            'barang_id' => ['required', 'exists:barang,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
        ]);

        $barangMasuk = DB::transaction(function () use ($validated) {
            $barangMasuk = BarangMasuk::create($validated);
            Barang::whereKey($validated['barang_id'])->increment('stok', $validated['jumlah']);
            ActivityLogger::log('api.barang_masuk.created', $barangMasuk, 'API mencatat barang masuk dan menambah stok', ['after' => $barangMasuk->toArray()]);

            return $barangMasuk->load('barang');
        });

        return response()->json(['message' => 'Barang masuk berhasil dicatat.', 'data' => $barangMasuk], 201);
    }
}

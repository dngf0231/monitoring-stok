<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('barang_keluar.view'), 403);

        return response()->json(
            BarangKeluar::with('barang', 'user')->latest()->paginate((int) $request->input('per_page', 10))
        );
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('barang_keluar.create'), 403);

        $validated = $request->validate([
            'barang_id' => ['required', 'exists:barang,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
        ]);

        $barangKeluar = BarangKeluar::create([
            ...$validated,
            'user_id' => auth()->id(),
            'status' => 'pending',
        ])->load('barang', 'user');
        ActivityLogger::log('api.barang_keluar.created', $barangKeluar, 'API mengajukan barang keluar', ['after' => $barangKeluar->toArray()], $request);

        return response()->json([
            'message' => 'Permintaan barang keluar dibuat dengan status pending dan menunggu approval admin.',
            'data' => $barangKeluar,
        ], 201);
    }

    public function approve(BarangKeluar $barangKeluar)
    {
        abort_unless(auth()->user()->hasPermission('barang_keluar.approve'), 403);

        return DB::transaction(function () use ($barangKeluar) {
            if ($barangKeluar->status !== 'pending') {
                return response()->json(['message' => 'Permintaan sudah diproses sebelumnya.'], 422);
            }

            $barang = Barang::lockForUpdate()->findOrFail($barangKeluar->barang_id);

            if ($barang->stok < $barangKeluar->jumlah) {
                return response()->json(['message' => 'Stok barang tidak mencukupi.'], 422);
            }

            $barang->decrement('stok', $barangKeluar->jumlah);
            $barangKeluar->update(['status' => 'approved']);
            ActivityLogger::log('api.barang_keluar.approved', $barangKeluar, 'API menyetujui barang keluar dan mengurangi stok', [
                'barang_id' => $barang->id,
                'jumlah' => $barangKeluar->jumlah,
            ]);

            return response()->json(['message' => 'Barang keluar disetujui dan stok dipotong.', 'data' => $barangKeluar->fresh()->load('barang', 'user')]);
        });
    }

    public function reject(BarangKeluar $barangKeluar)
    {
        abort_unless(auth()->user()->hasPermission('barang_keluar.reject'), 403);

        if ($barangKeluar->status !== 'pending') {
            return response()->json(['message' => 'Hanya permintaan pending yang bisa ditolak.'], 422);
        }

        $barangKeluar->update(['status' => 'rejected']);
        ActivityLogger::log('api.barang_keluar.rejected', $barangKeluar, 'API menolak barang keluar', ['id' => $barangKeluar->id]);

        return response()->json(['message' => 'Permintaan barang keluar ditolak.', 'data' => $barangKeluar->fresh()->load('barang', 'user')]);
    }
}

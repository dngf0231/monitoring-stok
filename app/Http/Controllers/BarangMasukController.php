<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Support\ActivityLogger;
use Illuminate\Support\Facades\DB; // Tambahkan ini

class BarangMasukController extends Controller
{
    public function index()
    {
        return view('barang_masuk.index', [
            'barang' => Barang::all(),
        ]);
    }

    public function datatable(Request $request)
    {
        $columns = ['barang.nama', 'jumlah', 'tanggal', 'created_at'];
        $query = BarangMasuk::query()->with('barang')->select('barang_masuk.*');
        $recordsTotal = BarangMasuk::count();
        $search = $request->input('search.value');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('jumlah', 'like', "%{$search}%")
                    ->orWhere('tanggal', 'like', "%{$search}%")
                    ->orWhereHas('barang', function ($barang) use ($search) {
                        $barang->where('nama', 'like', "%{$search}%")
                            ->orWhere('kode', 'like', "%{$search}%");
                    });
            });
        }

        $recordsFiltered = $query->count();
        $orderIndex = (int) $request->input('order.0.column', 3);
        $orderDir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';

        if (($columns[$orderIndex] ?? '') === 'barang.nama') {
            $query->join('barang', 'barang.id', '=', 'barang_masuk.barang_id')->orderBy('barang.nama', $orderDir);
        } else {
            $query->orderBy($columns[$orderIndex] ?? 'created_at', $orderDir);
        }

        $rows = $query->skip((int) $request->input('start', 0))
            ->take((int) $request->input('length', 10))
            ->get()
            ->map(fn (BarangMasuk $item) => [
                'barang' => '<span class="fw-bold">' . e($item->barang->nama) . '</span><br><small class="text-muted">Kode: ' . e($item->barang->kode) . '</small>',
                'jumlah' => '<span class="badge bg-success">+' . $item->jumlah . '</span>',
                'tanggal' => \Carbon\Carbon::parse($item->tanggal)->format('d M Y'),
                'waktu' => $item->created_at->diffForHumans(),
            ]);

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    public function store(Request $request) 
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date'
        ]);

        // Gunakan Transaction agar jika salah satu gagal, semua dibatalkan
        DB::beginTransaction();

        try {
            // 1. Simpan transaksi
            $barangMasuk = BarangMasuk::create($request->only(['barang_id', 'jumlah', 'tanggal']));

            // 2. Update stok barang
            $barang = Barang::findOrFail($request->barang_id);
            $barang->stok += $request->jumlah;
            $barang->save();
            ActivityLogger::log('barang_masuk.created', $barangMasuk, 'Mencatat barang masuk dan menambah stok', [
                'barang' => $barang->only(['id', 'kode', 'nama', 'stok']),
                'jumlah' => (int) $request->jumlah,
            ]);

            DB::commit(); // Simpan perubahan permanen
            flash_success('Stok berhasil ditambahkan!');
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollback(); // Batalkan semua jika ada error
            flash_error('Gagal memproses data: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}

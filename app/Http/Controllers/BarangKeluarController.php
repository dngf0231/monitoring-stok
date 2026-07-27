<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\BarangKeluar;
use Illuminate\Support\Facades\DB; // WAJIB ADA untuk DB::transaction

class BarangKeluarController extends Controller
{
    // TAMPILAN
    public function index()
    {
        return view('barang_keluar.index', [
            'barang' => Barang::all(),
        ]);
    }

    public function datatable(Request $request)
    {
        $columns = ['barang.nama', 'jumlah', 'tanggal', 'status'];
        $query = BarangKeluar::query()->with('barang', 'user')->select('barang_keluar.*');
        $recordsTotal = BarangKeluar::count();
        $search = $request->input('search.value');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('jumlah', 'like', "%{$search}%")
                    ->orWhere('tanggal', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('barang', fn ($barang) => $barang->where('nama', 'like', "%{$search}%"));
            });
        }

        $recordsFiltered = $query->count();
        $orderIndex = (int) $request->input('order.0.column', 2);
        $orderDir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';

        if (($columns[$orderIndex] ?? '') === 'barang.nama') {
            $query->join('barang', 'barang.id', '=', 'barang_keluar.barang_id')->orderBy('barang.nama', $orderDir);
        } else {
            $query->orderBy($columns[$orderIndex] ?? 'tanggal', $orderDir);
        }

        $rows = $query->skip((int) $request->input('start', 0))
            ->take((int) $request->input('length', 10))
            ->get()
            ->map(function (BarangKeluar $item) {
                $status = match ($item->status) {
                    'approved' => '<span class="badge bg-success"><i class="fas fa-check"></i> Approved</span>',
                    'rejected' => '<span class="badge bg-danger"><i class="fas fa-times"></i> Rejected</span>',
                    default => '<span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Pending</span>',
                };

                $actions = '';
                if (auth()->user()->role === 'admin') {
                    $actions = $item->status === 'pending'
                        ? '<div class="d-flex justify-content-center gap-2">
                            <form action="' . route('barang_keluar.approve', $item->id) . '" method="POST">' . csrf_field() . '<button class="btn btn-sm btn-success"><i class="fas fa-check"></i> Approve</button></form>
                            <form action="' . route('barang_keluar.reject', $item->id) . '" method="POST">' . csrf_field() . '<button class="btn btn-sm btn-danger"><i class="fas fa-times"></i> Reject</button></form>
                        </div>'
                        : '<span class="text-muted small">Selesai</span>';
                }

                return [
                    'barang' => '<span class="fw-bold">' . e($item->barang->nama) . '</span><br><small class="text-muted">ID Transaksi: #OUT-' . $item->id . '</small>',
                    'jumlah' => '<span class="fw-bold">' . $item->jumlah . '</span>',
                    'tanggal' => \Carbon\Carbon::parse($item->tanggal)->format('d M Y'),
                    'status' => $status,
                    'aksi' => $actions,
                ];
            });

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    // SIMPAN PERMINTAAN (USER)
    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id', // Pastikan nama tabel benar (barangs atau barang)
            'jumlah'    => 'required|integer|min:1',
            'tanggal'   => 'required|date',
        ]);

        BarangKeluar::create([
            'barang_id' => $request->barang_id,
            'jumlah'    => $request->jumlah,
            'tanggal'   => $request->tanggal,
            'user_id'   => auth()->id(),
            'status'    => 'pending'
        ]);

        flash_success('Permintaan barang keluar dikirim');
        return back();
    }

    // APPROVE (ADMIN)
    public function approve($id)
    {
        return DB::transaction(function () use ($id) {
            // 1. Ambil data dengan Lock untuk keamanan
            $keluar = BarangKeluar::findOrFail($id);

            // 2. Cek jika sudah bukan pending
            if ($keluar->status !== 'pending') {
                flash_error('Permintaan ini sudah diproses sebelumnya.');
                return back();
            }

            $barang = Barang::findOrFail($keluar->barang_id);

            // 3. Cek apakah stok cukup
            if ($barang->stok < $keluar->jumlah) {
                flash_error('Stok barang tidak mencukupi untuk disetujui.');
                return back();
            }

            // 4. Kurangi stok dan update status secara bersamaan
            $barang->decrement('stok', $keluar->jumlah);
            $keluar->update(['status' => 'approved']);

            flash_success('Barang keluar disetujui dan stok dipotong.');
            return back();
        });
    }

    // REJECT (ADMIN)
    public function reject($id)
    {
        $keluar = BarangKeluar::findOrFail($id);
        
        if ($keluar->status !== 'pending') {
            flash_error('Hanya permintaan pending yang bisa ditolak.');
            return back();
        }

        $keluar->update(['status' => 'rejected']);
        flash_info('Permintaan barang keluar ditolak.');
        return back();
    }
}

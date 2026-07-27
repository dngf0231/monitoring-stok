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
            'data'   => BarangKeluar::with('barang', 'user')->latest()->get()
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
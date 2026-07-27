<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    public function index()
    {
        return view('barang.index', [
            'data' => Barang::latest()->get() // Mengurutkan dari yang terbaru
        ]);
    }

    public function store(Request $r)
    {
        // 1. Validasi WAJIB agar tidak error SQL 'Column cannot be null'
        $validated = $r->validate([
            'kode' => 'required|unique:barang,kode',
            'nama' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
        ]);

        // 2. Gunakan $validated, jangan $r->all()
        Barang::create($validated);

        flash_success('Barang berhasil ditambahkan');
        return redirect()->route('barang.index');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'kode' => 'required|unique:barang,kode,' . $id,
            'nama' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update($validated);

        flash_success('Data barang berhasil diperbarui');
        return redirect()->back();
    }

    public function destroy($id)
    {
        try {
            $barang = Barang::findOrFail($id);
            
            // Cek relasi agar database tidak error 'Constraint Violation'
            // Ganti 'barangMasuk' & 'barangKeluar' sesuai nama method di Model Barang kamu
            if($barang->barangMasuk()->exists() || $barang->barangKeluar()->exists()) {
                flash_error('Gagal hapus! Barang ini sudah memiliki riwayat transaksi.');
                return redirect()->back();
            }

            $barang->delete();
            flash_success('Barang berhasil dihapus');
            return redirect()->back();

        } catch (\Exception $e) {
            flash_error('Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
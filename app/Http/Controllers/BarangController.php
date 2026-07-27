<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;

class BarangController extends Controller
{
    public function index()
    {
        return view('barang.index');
    }

    public function datatable(Request $request)
    {
        $columns = ['kode', 'nama', 'stok'];
        $query = Barang::query();
        $recordsTotal = $query->count();
        $search = $request->input('search.value');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('stok', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = $query->count();
        $orderIndex = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($columns[$orderIndex] ?? 'kode', $orderDir);

        $rows = $query->skip((int) $request->input('start', 0))
            ->take((int) $request->input('length', 10))
            ->get()
            ->map(function (Barang $barang) {
                $stokClass = $barang->stok <= 5 ? 'bg-danger' : 'bg-success';
                $actions = '';

                if (auth()->user()->role === 'admin') {
                    $actions = '
                        <button onclick="openEditModal(' . $barang->id . ', \'' . e($barang->kode) . '\', \'' . e($barang->nama) . '\', ' . $barang->stok . ')" class="btn btn-sm btn-info text-white">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button onclick="confirmDelete(' . $barang->id . ')" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i> Hapus
                        </button>';
                } else {
                    $actions = '<span class="text-muted small">Hanya Lihat</span>';
                }

                return [
                    'kode' => '<span class="fw-bold text-primary">' . e($barang->kode) . '</span>',
                    'nama' => '<span class="fw-medium">' . e($barang->nama) . '</span>',
                    'stok' => '<span class="badge ' . $stokClass . '">' . $barang->stok . ' Unit</span>',
                    'aksi' => '<div class="text-end">' . $actions . '</div>',
                ];
            });

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
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

@extends('layouts.app')

@section('title', 'Data Barang | Stock ATK')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>Manajemen Data Barang</h1>
        <p class="text-muted">Kelola daftar stok barang ATK Anda di sini.</p>
    </div>
    <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-slate-200 text-sm font-medium">
        📅 {{ date('d M Y') }}
    </div>
</div>

{{-- FORM TAMBAH BARANG: Hanya muncul untuk Admin --}}
@if(auth()->user()->role == 'admin')
<div class="mb-4 bg-white p-4 rounded-lg shadow-sm border border-slate-200">
    <h5 class="font-bold mb-3 text-slate-700">Tambah Barang Baru</h5>
    <form action="{{ route('barang.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="kode" class="form-label">Kode Barang</label>
                <input type="text" class="form-control" id="kode" name="kode" placeholder="Contoh: BRG-001" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="nama" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Barang" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="stok" class="form-label">Stok Awal</label>
                <input type="number" class="form-control" id="stok" name="stok" placeholder="Jumlah" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-plus"></i> Simpan Barang
        </button>
    </form>
</div>
@else
<div class="mb-4 p-3 bg-blue-50 text-blue-700 rounded-lg border border-blue-100">
    <i class="fas fa-info-circle"></i>
    <span class="ml-2 text-sm font-medium">Anda masuk sebagai <strong>User</strong>. Fitur tambah, edit, dan hapus dinonaktifkan.</span>
</div>
@endif

{{-- TABEL DATA --}}
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">Daftar Barang</h5>
    </div>
    <div class="card-body">
        @if($data->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Kode</th>
                        <th scope="col">Nama Barang</th>
                        <th scope="col">Stok Tersedia</th>
                        <th scope="col" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $b)
                    <tr>
                        <td class="fw-bold text-primary">{{ $b->kode }}</td>
                        <td class="fw-medium">{{ $b->nama }}</td>
                        <td>
                            <span class="badge {{ $b->stok <= 5 ? 'bg-danger' : 'bg-success' }}">
                                {{ $b->stok }} Unit
                            </span>
                        </td>
                        <td class="text-end">
                            @if(auth()->user()->role == 'admin')
                            <button onclick="openEditModal('{{ $b->id }}', '{{ $b->kode }}', '{{ $b->nama }}', '{{ $b->stok }}')" class="btn btn-sm btn-info text-white">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button onclick="confirmDelete('{{ $b->id }}')" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                            @else
                            <span class="text-muted text-xs">Hanya Lihat</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-box fa-3x text-muted mb-3"></i>
            <p class="text-muted">Belum ada data barang</p>
        </div>
        @endif
    </div>
</div>
@endsection

{{-- MODAL EDIT --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Perbarui Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label for="edit_kode" class="form-label">Kode Barang</label>
                        <input type="text" id="edit_kode" name="kode" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama" class="form-label">Nama Barang</label>
                        <input type="text" id="edit_nama" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_stok" class="form-label">Stok</label>
                        <input type="number" id="edit_stok" name="stok" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitEditForm()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentEditId = null;

    function openEditModal(id, kode, nama, stok) {
        currentEditId = id;
        document.getElementById('edit_kode').value = kode;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_stok').value = stok;
        document.getElementById('editForm').action = `/barang/${id}`;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }

    function submitEditForm() {
        if (currentEditId) {
            document.getElementById('editForm').submit();
        }
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Barang?',
            text: "Data ini tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }
</script>
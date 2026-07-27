@extends('layouts.app')

@section('title', 'Data Barang | Stock ATK')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>Manajemen Data Barang</h1>
        <p class="text-muted">Kelola daftar stok barang ATK Anda di sini.</p>
    </div>
    <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-slate-200 text-sm font-medium">
        <i class="fa-regular fa-calendar-days"></i> {{ date('d M Y') }}
    </div>
</div>

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
                <label for="stok" class="form-label">Stok Awal <span class="text-danger">*</span> hanya angka saja</label>
                <input type="number" class="form-control only-whole-number" id="stok" name="stok" placeholder="Jumlah" min="0" step="1" inputmode="numeric" required>
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

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">Daftar Barang</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="barangTable" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Stok Tersedia</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Perbarui Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_kode" class="form-label">Kode Barang</label>
                        <input type="text" id="edit_kode" name="kode" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama" class="form-label">Nama Barang</label>
                        <input type="text" id="edit_nama" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_stok" class="form-label">Stok <span class="text-danger">*</span> hanya angka saja</label>
                        <input type="number" id="edit_stok" name="stok" class="form-control only-whole-number" min="0" step="1" inputmode="numeric" required>
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

<form id="deleteBarangForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    let currentEditId = null;

    $('#barangTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('barang.datatable') }}",
        columns: [
            { data: 'kode', name: 'kode' },
            { data: 'nama', name: 'nama' },
            { data: 'stok', name: 'stok' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ]
    });

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
            text: 'Data ini tidak bisa dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteBarangForm').action = `/barang/${id}`;
                document.getElementById('deleteBarangForm').submit();
            }
        });
    }
</script>
@endpush

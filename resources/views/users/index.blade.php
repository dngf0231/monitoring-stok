@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Manajemen Pengguna</h1>
    @can('create', \App\Models\User::class)
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Tambah Pengguna
    </a>
    @endcan
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">Daftar Pengguna</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="usersTable" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status Akun</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pengguna <strong id="userName"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Perhatian:</strong>
                    <ul class="mb-0">
                        <li>Tidak dapat menghapus akun admin</li>
                        <li>Tidak dapat menghapus akun Anda sendiri</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteAction()">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<form id="deleteUserForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    let userIdToDelete = null;

    $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('users.datatable') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'role', name: 'role' },
            { data: 'status', name: 'status' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']]
    });

    function confirmDelete(id, name) {
        userIdToDelete = id;
        document.getElementById('userName').textContent = name;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    function confirmDeleteAction() {
        if (userIdToDelete) {
            document.getElementById('deleteUserForm').action = "{{ route('users.destroy', ':id') }}".replace(':id', userIdToDelete);
            document.getElementById('deleteUserForm').submit();
        }
    }
</script>
@endpush

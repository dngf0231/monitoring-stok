@extends('layouts.app')

@section('title', 'Manajemen Role Akses')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Manajemen Role Akses</h1>
    @can('create', \App\Models\Role::class)
    <a href="{{ route('roles.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Role
    </a>
    @endcan
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">Daftar Role Akses</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="rolesTable" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama Role</th>
                        <th>Deskripsi</th>
                        <th>Pengguna</th>
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
                <p>Apakah Anda yakin ingin menghapus role <strong id="roleName"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Perhatian:</strong> Tidak dapat menghapus role admin dan role yang masih memiliki pengguna!
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

<form id="deleteRoleForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    let roleIdToDelete = null;

    $('#rolesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('roles.datatable') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'description', name: 'description' },
            { data: 'users', name: 'users', orderable: false, searchable: false },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']]
    });

    function confirmDelete(id, name) {
        roleIdToDelete = id;
        document.getElementById('roleName').textContent = name;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    function confirmDeleteAction() {
        if (roleIdToDelete) {
            document.getElementById('deleteRoleForm').action = "{{ route('roles.destroy', ':id') }}".replace(':id', roleIdToDelete);
            document.getElementById('deleteRoleForm').submit();
        }
    }
</script>
@endpush

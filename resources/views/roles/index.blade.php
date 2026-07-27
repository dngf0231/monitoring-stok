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
            @if($roles->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Nama Role</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col">Pengguna</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>
                                <span class="badge {{ $role->name === 'admin' ? 'bg-danger' : 'bg-success' }}">
                                    {{ $role->name }}
                                </span>
                            </td>
                            <td>{{ $role->description ?? '-' }}</td>
                            <td>
                                @if($role->users()->count() > 0)
                                <span class="badge bg-info">{{ $role->users()->count() }} Pengguna</span>
                                @else
                                <span class="badge bg-secondary">0 Pengguna</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @can('view', $role)
                                <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-info text-white">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                @can('update', $role)
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-warning text-white">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete', $role)
                                <button type="button" class="btn btn-sm btn-danger"
                                    onclick="confirmDelete({{ $role->id }}, '{{ $role->name }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-user-shield fa-3x text-muted mb-3"></i>
                <p class="text-muted">Belum ada role yang dibuat</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Konfirmasi Hapus -->
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

    <script>
        let roleIdToDelete = null;

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
    @endsection

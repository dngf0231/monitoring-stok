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
        @if($users->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 32px; height: 32px; min-width: 32px;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="ms-2">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-success' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="text-end">
                            @can('view', $user)
                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info text-white">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endcan
                            @can('update', $user)
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning text-white">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan
                            @can('delete', $user)
                            <button type="button" class="btn btn-sm btn-danger"
                                onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
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
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <p class="text-muted">Belum ada pengguna yang dibuat</p>
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
                <p>Apakah Anda yakin ingin menghapus pengguna <strong>{{ $userName }}</strong>?</p>
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

<script>
    let userIdToDelete = null;

    function confirmDelete(id, name) {
        userIdToDelete = id;
        document.getElementById('userName').textContent = name;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    function confirmDeleteAction() {
        if (userIdToDelete) {
            window.location.href = "{{ route('users.destroy', ':id') }}".replace(':id', userIdToDelete);
        }
    }
</script>
@endsection

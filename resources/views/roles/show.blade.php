@extends('layouts.app')

@section('title', 'Detail Role')

@section('content')
<div class="mb-4">
    <h1>Detail Role</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Manajemen Role</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $role->name }}</li>
        </ol>
    </nav>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">Informasi Role</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">ID</label>
                <div class="fw-bold">{{ $role->id }}</div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Role</label>
                <div>
                    <span class="badge {{ $role->name === 'admin' ? 'bg-danger' : 'bg-success' }}">
                        {{ $role->name }}
                    </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 mb-3">
                <label class="form-label">Deskripsi</label>
                <div>{{ $role->description ?? '-' }}</div>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Total Pengguna</label>
                <div class="fw-bold">
                    @if($role->users()->count() > 0)
                    <span class="text-success">{{ $role->users()->count() }} Pengguna</span>
                    @else
                    <span class="text-muted">0 Pengguna</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Pengguna yang Ditugaskan</h5>
        @if($role->users()->count() > 0)
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-info text-white">
            <i class="fas fa-users"></i> Lihat Semua Pengguna
        </a>
        @endif
    </div>
    <div class="card-body">
        @if($role->users()->count() > 0)
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
                    @foreach($role->users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-success' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info text-white">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
            <p class="text-muted">Belum ada pengguna yang ditugaskan ke role ini</p>
        </div>
        @endif
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Role
    </a>
    @can('update', $role)
    <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning text-white">
        <i class="fas fa-edit"></i> Edit Role
    </a>
    @endcan
</div>
@endsection

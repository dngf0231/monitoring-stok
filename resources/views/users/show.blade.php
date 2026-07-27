@extends('layouts.app')

@section('title', 'Detail Pengguna')

@section('content')
<div class="mb-4">
    <h1>Detail Pengguna</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Manajemen Pengguna</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
        </ol>
    </nav>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex align-items-center gap-3">
        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
            style="width: 48px; height: 48px; min-width: 48px; font-size: 1.5rem; font-weight: bold;">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <h5 class="card-title mb-0">{{ $user->name }}</h5>
            <small class="text-muted">ID: {{ $user->id }}</small>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <div class="fw-bold">{{ $user->email }}</div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Role</label>
                <div>
                    <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-success' }}">
                        {{ $user->role }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">Informasi Tambahan</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">ID Pengguna</label>
                <div class="fw-bold">{{ $user->id }}</div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Status</label>
                <div class="badge bg-success">Aktif</div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Tanggal Pembuatan Akun</label>
                <div>{{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Pengguna
    </a>
    @can('update', $user)
    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning text-white">
        <i class="fas fa-edit"></i> Edit Pengguna
    </a>
    @endcan
</div>
@endsection

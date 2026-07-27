@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
<div class="mb-4">
    <h1>Edit Pengguna</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Manajemen Pengguna</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.show', $user) }}">Detail Pengguna</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Pengguna</li>
        </ol>
    </nav>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">Formulir Edit Pengguna</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name"
                        placeholder="Masukkan nama lengkap" value="{{ old('name', $user->name) }}" required autofocus>
                    <div class="form-text">
                        Nama lengkap pengguna.
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email"
                        placeholder="Masukkan email" value="{{ old('email', $user->email) }}" required>
                    <div class="form-text">
                        Email harus unik dan valid.
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role">
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role', $user->role) === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">
                        Pilih role untuk pengguna sesuai daftar role akses.
                        <strong class="text-warning">Perhatian: User tidak dapat mengubah role atau password sendiri!</strong>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status Akun</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <div class="form-text">
                        User hanya bisa login jika status akun Active.
                    </div>
                </div>
            </div>

            <hr>

            <h6 class="mb-3">Keamanan (Password)</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password (Opsional)</label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Kosongkan jika tidak ingin mengubah password">
                    <div class="form-text">
                        Kosongkan untuk mempertahankan password saat ini. Minimal 8 karakter jika diisi.
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password (Opsional)</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                        placeholder="Konfirmasi password">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('users.show', $user) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Detail
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

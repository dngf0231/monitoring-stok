@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')

@section('content')
<div class="mb-4">
    <h1>Tambah Pengguna Baru</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Manajemen Pengguna</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Pengguna</li>
        </ol>
    </nav>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">Formulir Tambah Pengguna</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            @method('POST')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name"
                        placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required autofocus>
                    <div class="form-text">
                        Nama lengkap pengguna.
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email"
                        placeholder="Masukkan email" value="{{ old('email') }}" required>
                    <div class="form-text">
                        Email harus unik dan valid.
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="" disabled selected>Pilih role...</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role', 'user') === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">
                        Pilih role untuk pengguna sesuai daftar role akses.
                    </div>
                </div>
            </div>

            <hr>

            <h6 class="mb-3">Keamanan</h6>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Masukkan password" required minlength="8">
                    <div class="form-text">
                        Password minimal 8 karakter.
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                        placeholder="Konfirmasi password" required>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Tambah Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

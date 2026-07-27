@extends('layouts.app')

@section('title', 'Tambah Role Baru')

@section('content')
<div class="mb-4">
    <h1>Tambah Role Baru</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Manajemen Role</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Role</li>
        </ol>
    </nav>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">Formulir Tambah Role</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            @method('POST')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nama Role <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name"
                        placeholder="Masukkan nama role (misal: admin, user)"
                        value="{{ old('name') }}" required autofocus>
                    <div class="form-text">
                        Nama role harus unik dan hanya menggunakan huruf kecil dan angka.
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description"
                        rows="4" placeholder="Masukkan deskripsi role">{{ old('description') }}</textarea>
                    <div class="form-text">
                        Deskripsi role ini akan ditampilkan sebagai penjelasan untuk pengguna lain.
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Permission</label>
                <div class="row g-3">
                    @foreach($permissionGroups as $group => $permissions)
                    <div class="col-md-6 col-xl-4">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="fw-semibold mb-2">{{ $group }}</div>
                            @foreach($permissions as $key => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $key }}" id="permission_{{ str_replace('.', '_', $key) }}" {{ in_array($key, old('permissions', []), true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="permission_{{ str_replace('.', '_', $key) }}">{{ $label }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

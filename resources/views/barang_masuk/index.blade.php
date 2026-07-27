@extends('layouts.app')

@section('title', 'Barang Masuk | Stock ATK')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>Barang Masuk</h1>
        <p class="text-muted">Catat penambahan stok barang yang masuk ke gudang.</p>
    </div>
    <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-slate-200 text-sm font-medium">
        📅 {{ date('d M Y') }}
    </div>
</div>

{{-- FORM INPUT BARANG MASUK --}}
<div class="mb-4 bg-white p-4 rounded-lg shadow-sm border border-slate-200">
    <h5 class="font-bold mb-3">Form Input Stok Baru</h5>
    <form action="{{ route('barang_masuk.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="barang_id" class="form-label">Pilih Barang</label>
                <select name="barang_id" class="form-select" required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach($barang as $b)
                    <option value="{{ $b->id }}">{{ $b->nama }} (Stok: {{ $b->stok }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="jumlah" class="form-label">Jumlah Masuk</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" placeholder="Contoh: 50" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="tanggal" class="form-label">Tanggal Masuk</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan Stok Masuk
        </button>
    </form>
</div>

{{-- TABEL RIWAYAT --}}
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">Riwayat Barang Masuk</h5>
    </div>
    <div class="card-body">
        @if($data->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Nama Barang</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Waktu Input</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $d)
                    <tr>
                        <td>
                            <div>
                                <span class="fw-bold">{{ $d->barang->nama }}</span>
                                <br>
                                <small class="text-muted">Kode: {{ $d->barang->kode }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-success">
                                +{{ $d->jumlah }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($d->tanggal)->format('d M Y') }}</td>
                        <td class="text-muted small">
                            {{ $d->created_at->diffForHumans() }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-truck fa-3x text-muted mb-3"></i>
            <p class="text-muted">Belum ada riwayat transaksi barang masuk.</p>
        </div>
        @endif
    </div>
</div>
@endsection

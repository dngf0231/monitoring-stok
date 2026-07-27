@extends('layouts.app')

@section('title', 'Barang Keluar | Stock ATK')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>Barang Keluar</h1>
        <p class="text-muted">Kelola permintaan dan pengeluaran stok ATK.</p>
    </div>
    <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-slate-200 text-sm font-medium">
        📅 {{ date('d M Y') }}
    </div>
</div>

{{-- FORM INPUT: Khusus User untuk Mengajukan Permintaan --}}
@if(auth()->user()->role === 'user')
<div class="mb-4 bg-white p-4 rounded-lg shadow-sm border border-slate-200">
    <h5 class="font-bold mb-3">Ajukan Permintaan Barang</h5>
    <form action="{{ route('barang_keluar.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="barang_id" class="form-label">Pilih Barang</label>
                <select name="barang_id" class="form-select" required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach($barang as $b)
                    <option value="{{ $b->id }}">{{ $b->nama }} (Sisa: {{ $b->stok }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="jumlah" class="form-label">Jumlah Dibutuhkan</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" placeholder="Contoh: 5" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="tanggal" class="form-label">Tanggal Keperluan</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane"></i> Kirim Permintaan
        </button>
    </form>
</div>
@endif

{{-- TABEL DATA --}}
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">Daftar Pengeluaran & Status</h5>
    </div>
    <div class="card-body">
        @if($data->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Barang</th>
                        <th scope="col" class="text-center">Jumlah</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Status</th>
                        @if(auth()->user()->role === 'admin')
                        <th scope="col" class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $d)
                    <tr>
                        <td>
                            <span class="fw-bold">{{ $d->barang->nama }}</span>
                            <br>
                            <small class="text-muted">ID Transaksi: #OUT-{{ $d->id }}</small>
                        </td>
                        <td class="text-center fw-bold">{{ $d->jumlah }}</td>
                        <td>{{ \Carbon\Carbon::parse($d->tanggal)->format('d M Y') }}</td>
                        <td>
                            @if($d->status === 'pending')
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-clock"></i> Pending
                            </span>
                            @elseif($d->status === 'approved')
                            <span class="badge bg-success">
                                <i class="fas fa-check"></i> Approved
                            </span>
                            @else
                            <span class="badge bg-danger">
                                <i class="fas fa-times"></i> Rejected
                            </span>
                            @endif
                        </td>
                        @if(auth()->user()->role === 'admin')
                        <td class="text-center">
                            @if($d->status === 'pending')
                            <div class="d-flex justify-content-center gap-2">
                                <form action="{{ route('barang_keluar.approve', $d->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>
                                <form action="{{ route('barang_keluar.reject', $d->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                            </div>
                            @else
                            <span class="text-muted text-xs">Selesai</span>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
            <p class="text-muted">Belum ada riwayat permintaan barang.</p>
        </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Barang Keluar | Stock ATK')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>Barang Keluar</h1>
        <p class="text-muted">Kelola permintaan dan pengeluaran stok ATK.</p>
    </div>
    <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-slate-200 text-sm font-medium">
        <i class="fa-regular fa-calendar-days"></i> {{ date('d M Y') }}
    </div>
</div>

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

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">Daftar Pengeluaran & Status</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="barangKeluarTable" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>Barang</th>
                        <th class="text-center">Jumlah</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        @if(auth()->user()->role === 'admin')
                        <th class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('#barangKeluarTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('barang_keluar.datatable') }}",
        columns: [
            { data: 'barang', name: 'barang' },
            { data: 'jumlah', name: 'jumlah', className: 'text-center' },
            { data: 'tanggal', name: 'tanggal' },
            { data: 'status', name: 'status' },
            @if(auth()->user()->role === 'admin')
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' }
            @endif
        ],
        order: [[2, 'desc']]
    });
</script>
@endpush

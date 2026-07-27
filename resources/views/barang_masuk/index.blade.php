@extends('layouts.app')

@section('title', 'Barang Masuk | Stock ATK')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>Barang Masuk</h1>
        <p class="text-muted">Catat penambahan stok barang yang masuk ke gudang.</p>
    </div>
    <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-slate-200 text-sm font-medium">
        <i class="fa-regular fa-calendar-days"></i> {{ date('d M Y') }}
    </div>
</div>

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
                <label for="jumlah" class="form-label">Jumlah Masuk <span class="text-danger">*</span> hanya angka saja</label>
                <input type="number" class="form-control only-whole-number" id="jumlah" name="jumlah" placeholder="Contoh: 50" min="1" step="1" inputmode="numeric" required>
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

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">Riwayat Barang Masuk</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="barangMasukTable" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Tanggal</th>
                        <th>Waktu Input</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('#barangMasukTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('barang_masuk.datatable') }}",
        columns: [
            { data: 'barang', name: 'barang' },
            { data: 'jumlah', name: 'jumlah' },
            { data: 'tanggal', name: 'tanggal' },
            { data: 'waktu', name: 'waktu' }
        ],
        order: [[3, 'desc']]
    });
</script>
@endpush

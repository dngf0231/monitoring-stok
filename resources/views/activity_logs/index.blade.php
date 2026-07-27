@extends('layouts.app')

@section('title', 'Log Activity')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1>Log Activity</h1>
        <p class="text-muted">Riwayat aktivitas web dan API pada project ini.</p>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">Daftar Activity</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="activityLogsTable" class="table table-hover align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>Waktu</th>
                        <th>Channel</th>
                        <th>Aksi</th>
                        <th>User</th>
                        <th>Entity</th>
                        <th>Deskripsi</th>
                        <th>IP</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('#activityLogsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('activity_logs.datatable') }}",
        columns: [
            { data: 'created_at', name: 'created_at' },
            { data: 'channel', name: 'channel' },
            { data: 'action', name: 'action' },
            { data: 'user', name: 'user', orderable: false },
            { data: 'entity', name: 'entity', orderable: false },
            { data: 'description', name: 'description' },
            { data: 'ip_address', name: 'ip_address' }
        ],
        order: [[0, 'desc']]
    });
</script>
@endpush

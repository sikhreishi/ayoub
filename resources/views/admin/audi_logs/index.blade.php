@push('plugin-scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.responsive.min.js"></script>
@endpush
@push('plugin-styles')
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.11.5/css/responsive.dataTables.min.css" rel="stylesheet" />
@endpush
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Audit Logs</h2>
    <div class="table-responsive">
        <x-data-table
            title="Audit Logs"
            table-id="audit-logs-table"
            fetch-url="{{ route('admin.audi_logs.data') }}"
            :columns="['ID', 'Admin', 'Action', 'Table', 'Record ID', 'Old Values', 'New Values', 'Time', 'IP', 'User Agent']"
            :columns-config="[
                ['data' => 'id', 'name' => 'id'],
                ['data' => 'admin', 'name' => 'admin'],
                ['data' => 'action', 'name' => 'action'],
                ['data' => 'table_name', 'name' => 'table_name'],
                ['data' => 'record_id', 'name' => 'record_id'],
                ['data' => 'old_values', 'name' => 'old_values', 'orderable' => false, 'searchable' => false],
                ['data' => 'new_values', 'name' => 'new_values', 'orderable' => false, 'searchable' => false],
                ['data' => 'created_at', 'name' => 'created_at'],
                ['data' => 'ip_address', 'name' => 'ip_address'],
                ['data' => 'user_agent', 'name' => 'user_agent', 'orderable' => false, 'searchable' => false],
            ]"
        />
    </div>
</div>
@endsection

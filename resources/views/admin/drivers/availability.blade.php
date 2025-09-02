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

    <div class="table-responsive">
        <x-data-table
            title="Drivers"
            table-id="drivers-table"
            fetch-url="{{ route('admin.drivers.available.data') }}"
            :columns="['Name', 'Phone', 'Avatar', 'Last Ping', 'Actions']"
            :columns-config="[
                ['data' => 'name', 'name' => 'name'],
                ['data' => 'phone', 'name' => 'phone'],
                ['data' => 'avatar', 'name' => 'avatar', 'orderable' => false, 'searchable' => false],
                ['data' => 'last_ping', 'name' => 'last_ping'],
                ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
            ]"
        />
    </div>
@endsection


@push('custom-scripts')

@endpush

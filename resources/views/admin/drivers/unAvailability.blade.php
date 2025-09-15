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


<h4 class="mb-4">Unavailable Drivers</h4>

<div class="table-responsive">
    <x-data-table
        title="Unavailable Drivers"
        table-id="drivers-table"
        fetch-url="{{ route('admin.drivers.unavailable.data') }}"
        :columns="['Name', 'Phone', 'Avatar' 'Actions']"
        :columns="[__('dashboard.users.name'), __('dashboard.users.phone'), __('dashboard.users.avatar'), __('dashboard.users.actions')]"

        :columns-config="[
            ['data' => 'name', 'name' => 'name'],
            ['data' => 'phone', 'name' => 'phone'],
            ['data' => 'avatar', 'name' => 'avatar', 'orderable' => false, 'searchable' => false],
            ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
        ]"
    />
</div>

@endsection


@push('custom-scripts')

@endpush

@push('plugin-scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.responsive.min.js"></script>
    @include('admin.contacts.script.script')
@endpush

@push('plugin-styles')
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.11.5/css/responsive.dataTables.min.css" rel="stylesheet" />
@endpush

@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Contact Statistics -->
        <div id="contact-stats" class="mb-4">
            <!-- Statistics will be loaded here via JavaScript -->
        </div>

        <!-- Messages Table -->
        <div class="table-responsive">

            <x-data-table title="Driver Messages (Driver Role Only)" table-id="driver-contacts-table"
                fetch-url="{{ route('admin.contacts.drivers') }}" :columns="['Name', 'Email', 'Phone', 'Role', 'Message', 'Actions']" :columns-config="[
                    ['data' => 'driver_name', 'name' => 'driver_name'],
                    ['data' => 'email', 'name' => 'email'],
                    ['data' => 'phone', 'name' => 'phone'],
                    ['data' => 'driver_role', 'name' => 'driver_role'],
                    ['data' => 'message', 'name' => 'message'],
                    ['data' => 'actions', 'name' => 'actions', 'orderable' => false, 'searchable' => false],
                ]" />
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .data-table {
            direction: ltr;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .card h3 {
            margin-bottom: 0;
            font-weight: bold;
        }
    </style>
@endpush

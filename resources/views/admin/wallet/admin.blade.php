@push('plugin-scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.responsive.min.js"></script>
</script>
@endpush
@push('plugin-styles')
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.11.5/css/responsive.dataTables.min.css" rel="stylesheet" />
@endpush
@extends('layouts.app')
@section('content')
    <div class="mb-2">
        <a class="btn btn-success" href="{{ route('admin.wallets.admin.pdf') }}" target="_blank">Download All Balances (PDF)</a>
    </div>
    <div class="table-responsive">
        <x-data-table title="Users" table-id="users-table" fetch-url="{{ route('admin.wallets.admin.data') }}"
            :columns="['User ID', 'User Name', 'Email', 'Phone', 'Balance', 'Created At', 'Last Transaction', 'Action']"
            :columns-config="[
                ['data' => 'user_id', 'name' => 'user_id'],
                ['data' => 'user_name', 'name' => 'user_name'],
                ['data' => 'email', 'name' => 'email'],
                ['data' => 'phone', 'name' => 'phone'],
                ['data' => 'balance', 'name' => 'balance'],
                ['data' => 'created_at', 'name' => 'created_at'],
                ['data' => 'last_transaction', 'name' => 'last_transaction'],
                ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false],
            ]" />
    </div>
@endsection

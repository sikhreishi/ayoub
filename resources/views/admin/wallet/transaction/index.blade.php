
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
        <x-data-table title="Wallet Transactions" table-id="transactions-table" fetch-url="{{ route('admin.wallets.transactions.getData') }}"
            :columns="['ID', 'Wallet ID', 'Owner Type', 'Owner Name', 'Transaction Type', 'Amount', 'Reference', 'Description', 'Date']"
            :columns-config="[
                ['data' => 'id', 'name' => 'id'],
                ['data' => 'wallet_id', 'name' => 'wallet_id'],
                ['data' => 'owner_type', 'name' => 'owner_type'],
                ['data' => 'owner_name', 'name' => 'owner_name'],
                ['data' => 'transaction_type', 'name' => 'transaction_type'],
                ['data' => 'amount', 'name' => 'amount'],
                ['data' => 'reference_type', 'name' => 'reference_type'],
                ['data' => 'description', 'name' => 'description'],
                ['data' => 'created_at', 'name' => 'created_at'],
            ]" />
    </div>
@endsection


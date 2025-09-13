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

<div class="mb-4">
    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('dashboard.wallet.back') }}
    </a>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ __('dashboard.wallet.wallet_information') }}</h4>
    <span class="badge bg-primary"><i class="fas fa-user"></i> {{ $user->name }}</span>
</div>

<div class="row g-4">
    <div class="col-md-12">
        <div class="balance-card p-4 text-center h-100 bg-primary text-white rounded">
            <h5 class="text-white-50 mb-3">{{ __('dashboard.wallet.current_balance') }}</h5>
            <p class="balance-amount mb-0">
                <strong>{{ __('dashboard.wallet.usd_balance') }}: </strong>
                {{ $usdBalance }}
            </p>
            <p class="balance-amount mb-0">
                <strong>{{ $userCurrency }} {{ __('dashboard.wallet.balance') }}: </strong>
                {{ $localBalance }}
            </p>
        </div>
    </div>
</div>

<div class="row g-4 mt-4">
    <div class="col-12">
        <x-data-table
            title="{{ __('dashboard.wallet.transactions') }}"
            table-id="transactions-table"
            fetch-url="{{ route('admin.profile.wallets.transactions.data', $user->id) }}"
            :columns="[__('dashboard.wallet.type'), __('dashboard.wallet.amount'), __('dashboard.wallet.payment_method'), __('dashboard.wallet.description'), __('dashboard.wallet.date')]"
            :columns-config="[
                ['data' => 'transaction_type', 'name' => 'transaction_type'],
                ['data' => 'amount', 'name' => 'amount'],
                ['data' => 'reference_type', 'name' => 'reference_type'],
                ['data' => 'description', 'name' => 'description'],
                ['data' => 'created_at', 'name' => 'created_at']
            ]"
        />
    </div>
</div>

@endsection

@push('custom-styles')
<style>
    body {
        direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
    }

    .balance-card {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        border-radius: 12px;
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
    }

    .balance-card:hover {
        transform: translateY(-4px);
    }

    .balance-amount {
        font-size: 2.2rem;
        font-weight: 700;
    }

    .card-header h5 {
        margin: 0;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
    }

    .card-body p {
        margin: 0;
    }

    .delete-btn:hover {
        opacity: 0.8;
    }

    @media (max-width: 767.98px) {
        .balance-amount {
            font-size: 1.8rem;
        }
    }
</style>
@endpush

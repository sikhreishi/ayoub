@extends('layouts.app')

@section('title', 'User Wallets')

@section('content')
<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">User Wallets</h4>
            <button class="btn btn-primary" onclick="location.reload()">Refresh</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Balance</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wallets as $wallet)
                            <tr>
                                <td>{{ $wallet->id }}</td>
                                <td>{{ $wallet->user ? $wallet->user->name : '-' }}</td>
                                <td>{{ $wallet->user ? $wallet->user->email : '-' }}</td>
                                <td>{{ $wallet->user ? $wallet->user->phone : '-' }}</td>
                                <td>{{ number_format($wallet->balance, 2) }}</td>
                                <td>{{ $wallet->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No wallets found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

<?php

namespace App\Http\Controllers\Admin\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;



class WalletController extends Controller
{

    public function show($userId)
    {
        $user = User::with([
            'wallet',
        ])->findOrFail($userId);

        if (!$user->wallet) {
            $user->wallet()->create(['balance' => 0.00]);
            $user->refresh(); // Reload with the new wallet
        }

        return view('profile.wallet.show', compact('user'));
    }

    public function getWalletTransactions(Request $request, $userId)
    {
        try {
            $user = User::with('wallet')->findOrFail($userId);

            if (!$user->wallet) {
                return datatables()->of(collect([]))->make(true);
            }

            $query = $user->wallet->transactions()->orderByDesc('created_at');

            return datatables()->of($query)
                ->addColumn('transaction_type', fn($txn) => ucfirst($txn->transaction_type))
                ->addColumn('amount', fn($txn) => number_format($txn->amount, 2) . ' JOD')
                ->addColumn('reference_type', fn($txn) => $txn->reference_type ? ucfirst($txn->reference_type) : '-')
                ->addColumn('reference_id', fn($txn) => $txn->reference_id ?? '-')
                ->addColumn('description', fn($txn) => $txn->description ?? '-')
                ->addColumn('created_at', fn($txn) => $txn->created_at ? $txn->created_at->format('Y-m-d H:i') : 'N/A')
                ->make(true);
        } catch (\Throwable $e) {
            Log::error('getWalletTransactions error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}

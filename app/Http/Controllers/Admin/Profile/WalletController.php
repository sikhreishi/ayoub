<?php

namespace App\Http\Controllers\Admin\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\Datatables\Facades\Datatables;
use App\Services\Currency\CurrencyService;
use App\Models\Wallet;



class WalletController extends Controller
{

    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function show($userId)
    {
        $user = User::with('country')->findOrFail($userId);

        if (!$user->wallet) {
            $user->wallet()->save(new Wallet(['balance' => 0.00]));
        }
        
        $userCurrency = $user->country->code ?? 'USD'; 

        $usdBalance = $user->wallet->balance;

        $localBalance = app(\App\Services\Currency\CurrencyService::class)->convertFromUSD($usdBalance, $userCurrency);

        return view('profile.wallet.show', compact('user', 'userCurrency', 'usdBalance', 'localBalance'));
    }

    public function getUserCurrency($user)
    {
        // Get the first country currency related to the user
        $countryCurrency = $user->country->countrycurrencies->first();

        // Return the currency code or default to USD if not available
        if ($countryCurrency) {
            return $countryCurrency->currency_code; // Assuming currency_code is part of the CountryCurrency model
        }

        return 'USD'; // Default to USD if no specific currency found for the country
    }


    public function getWalletTransactions(Request $request, $userId)
{
    try {
        $user = User::with('wallet')->findOrFail($userId);

        if (!$user->wallet) {
            $user->wallet()->save(new Wallet(['balance' => 0.00]));
        }

        // Fetch transactions, but ensure it's not empty
        $transactions = $user->wallet->transactions()->orderByDesc('created_at')->get();
        Log::info('Fetched ' . $transactions . ' transactions for user ID ' . $userId);
        // Check if transactions exist
        if ($transactions->isEmpty()) {
            return datatables()->of(collect([]))->make(true); // Return an empty response
        }

        // Return the transactions if they exist
        return datatables()->of($transactions)
            ->addColumn('transaction_type', fn($txn) => ucfirst($txn->transaction_type))
            ->addColumn('amount', fn($txn) => number_format($txn->amount, 2) . ' USD')
            ->addColumn('reference_type', fn($txn) => $txn->reference_type ? ucfirst($txn->reference_type) : '-')
            ->addColumn('reference_id', fn($txn) => $txn->reference_id ?? '-')
            ->addColumn('description', fn($txn) => $txn->description ?? '-')
            ->addColumn('created_at', fn($txn) => $txn->created_at ? $txn->created_at->format('Y-m-d H:i') : 'N/A')
            ->make(true);
    } catch (\Throwable $e) {
        Log::error('getWalletTransactions error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500); // Return the actual error message
    }
}

}

<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\WalletCode;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\AudiLogsService;
use Carbon\Carbon;

class DriverWalletController extends Controller
{
    public function getWalletBalance(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user->hasRole('driver')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Driver role required.'
                ], 403);
            }

            $wallet = $this->getOrCreateWallet($user->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'balance' => number_format($wallet->balance, 2),
                    'balance_raw' => floatval($wallet->balance),
                    'can_accept_rides' => $wallet->balance > 0,
                    'currency' => '$'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching wallet balance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch wallet balance'
            ], 500);
        }
    }

    public function redeemCode(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:255'
            ], [
                'code.required' => 'Wallet code is required',
                'code.string' => 'Invalid code format',
                'code.max' => 'Code is too long'
            ]);

            $user = $request->user();

            if (!$user->hasRole('driver')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only drivers can redeem wallet codes.'
                ], 403);
            }

            DB::beginTransaction();

            $walletCode = WalletCode::where('code', strtoupper(trim($request->code)))->first();

            if (!$walletCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid code. Please check the code and try again.'
                ], 400);
            }

            if (!$walletCode->isUnused()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This code has already been used and cannot be redeemed again.'
                ], 400);
            }

            $wallet = $this->getOrCreateWallet($user->id);
            $oldBalance = $wallet->balance;
            $rechargeAmount = $walletCode->balance;
            $newBalance = $oldBalance + $rechargeAmount;

            $wallet->update(['balance' => $newBalance]);

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $rechargeAmount,
                'transaction_type' => 'recharge',
                'reference_type' => 'recharge_code',
                'reference_id' => $walletCode->id,
                'description' => "Wallet recharged using code: {$walletCode->code}"
            ]);

            $walletCode->markAsUsed($user->id);

            AudiLogsService::storeLog('update', 'wallet_recharge', $wallet->id, [
                'old_balance' => $oldBalance,
                'code_used' => $walletCode->code,
                'user_id' => $user->id
            ], [
                'new_balance' => $newBalance,
                'amount_added' => $rechargeAmount
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Code redeemed successfully! Your wallet has been credited with $" . number_format($rechargeAmount, 2),
                'data' => [
                    'old_balance' => number_format($oldBalance, 2),
                    'new_balance' => number_format($newBalance, 2),
                    'amount_added' => number_format($rechargeAmount, 2),
                    'can_accept_rides' => $newBalance > 0,
                    'redeemed_at' => now()->toISOString()
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error redeeming wallet code: ' . $e->getMessage(), [
                'user_id' => $request->user()->id ?? null,
                'code' => $request->code ?? null
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to redeem code. Please try again later.'
            ], 500);
        }
    }

    public function getTransactionHistory(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user->hasRole('driver')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Driver role required.'
                ], 403);
            }

            $wallet = $this->getOrCreateWallet($user->id);

            $page = max(1, (int) $request->get('page', 1));
            $perPage = min(50, max(10, (int) $request->get('per_page', 20)));

            $transactions = WalletTransaction::where('wallet_id', $wallet->id)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $formattedTransactions = $transactions->getCollection()->map(function ($transaction) {
                $isCredit = $transaction->transaction_type === 'recharge';

                $createdAt = Carbon::parse($transaction->created_at);

                return [
                    'id' => $transaction->id,
                    'amount' => number_format($transaction->amount, 2),
                    'amount_raw' => floatval($transaction->amount),
                    'type' => $transaction->transaction_type,
                    'type_display' => ucfirst(str_replace('_', ' ', $transaction->transaction_type)),
                    'reference_type' => $transaction->reference_type,
                    'reference_display' => ucfirst(str_replace('_', ' ', $transaction->reference_type)),
                    'description' => $transaction->description,
                    'is_credit' => $isCredit,
                    'is_debit' => !$isCredit,
                    'date' => $createdAt->toISOString(),
                    'date_formatted' => $createdAt->format('M d, Y \a\t h:i A'),
                    'date_short' => $createdAt->format('M d, Y')
                ];
            });


            return response()->json([
                'success' => true,
                'data' => [
                    'transactions' => $formattedTransactions,
                    'wallet_balance' => number_format($wallet->balance, 2),
                    'wallet_balance_raw' => floatval($wallet->balance),
                    'pagination' => [
                        'current_page' => $transactions->currentPage(),
                        'last_page' => $transactions->lastPage(),
                        'per_page' => $transactions->perPage(),
                        'total' => $transactions->total(),
                        'has_more_pages' => $transactions->hasMorePages()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching transaction history: ' . $e->getMessage(), [
                'user_id' => $request->user()->id ?? null
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch transaction history'
            ], 500);
        }
    }

    public function getWalletStats(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user->hasRole('driver')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Driver role required.'
                ], 403);
            }

            $wallet = $this->getOrCreateWallet($user->id);

            $totalRecharged = WalletTransaction::where('wallet_id', $wallet->id)
                ->where('transaction_type', 'recharge')
                ->sum('amount');

            $totalSpent = WalletTransaction::where('wallet_id', $wallet->id)
                ->where('transaction_type', 'deduction')
                ->sum('amount');

            $transactionCount = WalletTransaction::where('wallet_id', $wallet->id)->count();

            $lastTransaction = WalletTransaction::where('wallet_id', $wallet->id)
                ->orderBy('created_at', 'desc')
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'current_balance' => number_format($wallet->balance, 2),
                    'current_balance_raw' => floatval($wallet->balance),
                    'total_recharged' => number_format($totalRecharged, 2),
                    'total_recharged_raw' => floatval($totalRecharged),
                    'total_spent' => number_format($totalSpent, 2),
                    'total_spent_raw' => floatval($totalSpent),
                    'transaction_count' => $transactionCount,
                    'can_accept_rides' => $wallet->balance > 0,
                    'last_transaction' => $lastTransaction ? [
                        'amount' => number_format($lastTransaction->amount, 2),
                        'type' => $lastTransaction->transaction_type,
                        'date' => Carbon::parse($lastTransaction->created_at)->format('M d, Y \a\t h:i A'),
                        'description' => $lastTransaction->description
                    ] : null

                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching wallet stats: ' . $e->getMessage(), [
                'user_id' => $request->user()->id ?? null
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch wallet statistics'
            ], 500);
        }
    }

    private function getOrCreateWallet($userId)
    {
        return Wallet::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0.00]
        );
    }
}

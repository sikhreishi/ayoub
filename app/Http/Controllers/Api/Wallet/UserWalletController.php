<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Services\Currency\CurrencyService;
use App\Models\WalletTransaction;
use App\Models\Wallet;
use App\Models\WalletCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\AudiLogsService;
use Carbon\Carbon;


class UserWalletController extends Controller
{
    public function show(Request $request, CurrencyService $fx)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

       
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

     
        $currencyCode = 'USD'; 
    if (!empty($user->country_id)) {
        $country = Country::with(['countrycurrencies.currency'])->find($user->country_id);

       
        if ($country && $country->countrycurrencies->isNotEmpty()) {
            $cc = $country->countrycurrencies->first();
            $currencyCode = $cc->currency->code
                ?? $cc->code
                ?? 'USD';
        } else {
    
            $currencyCode = match (strtoupper((string) $country?->code)) {
                'JOR' => 'JOD', // Jordan
                'SYR' => 'SYP', // Syria
                'USA', 'US' => 'USD', // United States
                default => 'USD',
            };
        }
    }
  
        $usdBalance     = (float) $wallet->balance;
        $localBalance   = $fx->convertFromUSD($usdBalance, $currencyCode);
        $exchangeRate   = $fx->getExchangeRate('USD', $currencyCode);

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $user->id,

                'amounts' => [
                    // 'USD' => [
                    //     'value'     => $usdBalance,
                    //     'formatted' => $fx->formatMoney($usdBalance, 'USD'),
                    // ],
                    'local' => [
                        'currency'  => $currencyCode,
                        'value'     => $localBalance,
                        'formatted' => $fx->formatMoney($localBalance, $currencyCode),
                    ],
                ],

                // 'fx' => [
                //     'base'          => 'USD',
                //     'target'        => $currencyCode,
                //     'rate_USD_to_target' => $exchangeRate, 
                // ],

                'updated_at' => optional($wallet->updated_at)->toIso8601String(),
            ],
        ]);
    }

    public function transactions(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        $request->validate([
            'type'     => 'nullable|in:recharge,deduction',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $type    = $request->query('type'); 
        $perPage = (int) $request->query('per_page', 0);

        $query = WalletTransaction::where('wallet_id', $wallet->id)
            ->when($type, fn($q) => $q->where('transaction_type', $type))
            ->with([
                'trip:id,vehicle_type_id,final_fare,status,pickup_name,dropoff_name,requested_at,completed_at',
                'trip.vehicleType:id,name',
            ])
            ->orderByDesc('id');

        $mapFn = function ($t) {
            $tripSummary = null;

            if ($t->transaction_type === 'deduction' && $t->trip) {
                $tripSummary = [
                    'id'           => $t->trip->id,
                    'status'       => $t->trip->status,
                    'final_fare'   => (float) $t->trip->final_fare,
                    'requested_at' => optional($t->trip->requested_at)->toIso8601String(),
                    'completed_at' => optional($t->trip->completed_at)->toIso8601String(),
                    'pickup_name'  => $t->trip->pickup_name,
                    'dropoff_name' => $t->trip->dropoff_name,
                    'vehicle_type' => $t->trip->vehicleType
                        ? ['id' => $t->trip->vehicleType->id, 'name' => $t->trip->vehicleType->name]
                        : null,
                ];
            }

            return [
                'id'                      => $t->id,
                'amount'                  => (float) $t->amount,
                'transaction_type'        => $t->transaction_type,   
                'reference_type'          => $t->reference_type,
                'reference_id'            => $t->reference_id,
                'trip_id'                 => $t->trip_id,             
                'payment_transaction_id'  => $t->payment_transaction_id,
                'description'             => $t->description,
                'trip'                    => $tripSummary,             
            ];
        };

        if ($perPage > 0) {
            $paginator = $query->paginate($perPage)->appends($request->query());
            $data = $paginator->getCollection()->map($mapFn)->values();

            return response()->json([
                'success' => true,
                'data'    => $data,
                'filters' => ['type' => $type],
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page'    => $paginator->lastPage(),
                    'per_page'     => $paginator->perPage(),
                    'total'        => $paginator->total(),
                    'has_more'     => $paginator->hasMorePages(),
                    'next'         => $paginator->nextPageUrl(),
                    'prev'         => $paginator->previousPageUrl(),
                ],
            ]);
        }

        $items = $query->limit(200)->get()->map($mapFn)->values();

        return response()->json([
            'success' => true,
            'data'    => $items,
            'filters' => ['type' => $type],
            'meta'    => ['count' => $items->count()],
        ]);
    }

}

<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Events\PaymentCompleted;

class PaymentService
{

    public function createInvoice(int $userId, int $rideId, float $amount, int $currencyId, ?int $gatewayId = null): Invoice
    {
        return Invoice::create([
            'user_id' => $userId,
            'ride_id' => $rideId,
            'amount' => $amount,
            'currency_id' => $currencyId,
            'payment_gateway_id' => $gatewayId,
            'status' => 'pending',
        ]);
    }


    public function initiatePayment(Invoice $invoice): PaymentTransaction
    {
        $transaction = PaymentTransaction::create([
            'invoice_id'           => $invoice->id,
            'user_id'              => $invoice->user_id,
            'payment_gateway_id'   => $invoice->payment_gateway_id,
            'amount'               => $invoice->amount,
            'status'               => 'initiated',
            'transaction_reference' => Str::uuid(),
        ]);

        return $transaction;
    }

    public function completePayment(PaymentTransaction $transaction, string $status, ?array $gatewayResponse = []): void
    {
        DB::transaction(function () use ($transaction, $status, $gatewayResponse) {
            $transaction->update([
                'status' => $status,
                'response_data' => $gatewayResponse,
            ]);

            if ($status === 'success') {
                $transaction->invoice->update([
                    'status' => 'paid',
                    'paid_at' => Carbon::now(),
                ]);
                event(new PaymentCompleted($transaction->invoice));
            } elseif ($status === 'failed') {
                $transaction->invoice->update([
                    'status' => 'failed',
                ]);
            }
        });
    }
}

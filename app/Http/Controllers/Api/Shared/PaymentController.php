<?php

namespace App\Http\Controllers\Api\Shared;

use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Models\{
    Invoice,
    PaymentTransaction,
};
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{

    protected PaymentService $payment;

    public function __construct(PaymentService $payment)
    {
        $this->payment = $payment;
    }

    public function createInvoiceForRide(Request $request)
    {
        $invoice = $this->payment->createInvoice(
            userId: Auth::id(),
            rideId: $request->ride_id,
            amount: $request->amount,
            currencyId: $request->currency_id,
            gatewayId: $request->gateway_id
        );

        return response()->json($invoice);
    }

    public function initiate(Request $request, Invoice $invoice)
    {
        $transaction = $this->payment->initiatePayment($invoice);

        return response()->json($transaction);
    }

    public function webhookCallback(Request $request)
    {
        $transaction = PaymentTransaction::where('transaction_reference', $request->ref)->firstOrFail();

        $this->payment->completePayment($transaction, 'success', $request->all());

        return response()->json(['message' => 'Payment completed']);
    }
}

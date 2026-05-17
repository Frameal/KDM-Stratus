<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/paymongo', function (Request $request) {
    $payload = $request->all();
    
    // Updated to listen for the Checkout Session success event
    if (isset($payload['data']['attributes']['type']) && $payload['data']['attributes']['type'] === 'checkout_session.payment.paid') {
        
        // Grab the Checkout Session ID
        $checkoutId = $payload['data']['attributes']['data']['id'];
        
        $transaction = \App\Models\Transaction::where('reference_id', $checkoutId)->where('status', 'pending')->first();
        
        if ($transaction) {
            $transaction->update(['status' => 'paid']);
            
            $user = \App\Models\User::find($transaction->user_id);
            $user->balance += $transaction->amount;
            $user->save();
        }
    }

    return response()->json(['status' => 'success']);
});
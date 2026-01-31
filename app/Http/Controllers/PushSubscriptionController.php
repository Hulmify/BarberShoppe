<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class PushSubscriptionController extends Controller
{
    /**
     * Update or create a push subscription for a customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Push Subscription Payload:', $request->all());
        
        \Illuminate\Support\Facades\Log::info('Push Subscription Request Received', [
            'endpoint' => $request->endpoint,
            'is_auth' => auth()->check(),
            'user_id' => auth()->id()
        ]);

        $request->validate([
            'endpoint' => 'required',
            'keys.auth' => 'required',
            'keys.p256dh' => 'required',
            'phone' => 'nullable|string',
        ]);

        $target = null;

        if ($request->phone) {
            $target = Customer::where('phone', $request->phone)->first();
            \Illuminate\Support\Facades\Log::info('Target identified as Customer via phone');
        } elseif (auth()->check()) {
            $target = auth()->user();
            \Illuminate\Support\Facades\Log::info('Target identified as Auth User');
        }

        if (!$target) {
            \Illuminate\Support\Facades\Log::warning('No target found for push subscription');
            return response()->json(['message' => 'Subscription target (customer or user) not found'], 404);
        }

        $target->updatePushSubscription(
            $request->endpoint,
            $request->keys['p256dh'],
            $request->keys['auth']
        );

        \Illuminate\Support\Facades\Log::info('Subscription updated successfully for target type: ' . get_class($target));

        return response()->json(['success' => true]);
    }

    /**
     * Delete a push subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'endpoint' => 'required',
            'phone' => 'nullable|string',
        ]);

        $target = null;

        if ($request->phone) {
            $target = Customer::where('phone', $request->phone)->first();
        } elseif (auth()->check()) {
            $target = auth()->user();
        }

        if ($target) {
            $target->deletePushSubscription($request->endpoint);
        }

        return response()->json(['success' => true]);
    }
}

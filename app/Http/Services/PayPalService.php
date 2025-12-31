<?php

namespace App\Http\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalService
{
    public function paymentCancel($token)
    {
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            $response = $provider->showOrderDetails($token);
            $orderId = $response['purchase_units'][0]['reference_id'] ?? null;

            $order = Order::find($orderId);
            if ($order) {
                $order->payment_status = 'failed';
                $order->order_status = 'cancelled';
                $order->save();
            }
        } catch (\Throwable $th) {
            Log::error('Failed payment cancellation', [
                'message' => $th->getMessage()
            ]);

            throw new \Exception('Payment cancellation failed.');
        }
    }

    public function paymentSuccess($token): ?string
    {
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($token);
            $status = $response['status'] ?? null;
            $orderId = $response['purchase_units'][0]['reference_id'] ?? null;

            if ($status === 'COMPLETED' && $orderId) {
                $order = Order::find($orderId);
                if ($order) {
                    $order->payment_status = 'paid';
                    $order->order_status = 'processing';
                    $order->save();
                }

                return $orderId;
            }

            return null;
        } catch (\Throwable $th) {
            Log::error('Failed payment success', [
                'message' => $th->getMessage()
            ]);

            throw new \Exception('Payment processing failed.');
        }
    }
}

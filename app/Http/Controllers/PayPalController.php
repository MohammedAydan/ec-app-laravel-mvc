<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function paymentCancel()
    {
        return redirect()
            ->route('orders.index')
            ->with('error', 'You have canceled the transaction.');
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function paymentSuccess(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect()->route('orders.index')->with('error', 'Missing payment token.');
        }

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($token);

            $status = $response['status'] ?? null;
            if ($status === 'COMPLETED') {
                $orderId = $response['purchase_units'][0]['reference_id'] ?? null;
                if ($orderId) {
                    // Here you can update your order status in the database if needed
                    $order = Order::find($orderId);
                    if ($order) {
                        $order->payment_status = 'paid';
                        $order->save();
                    }

                    return redirect()->route('orders.show', ['orderId' => $orderId])->with('success', 'Transaction complete.');
                }

                return redirect()->route('orders.index')->with('success', 'Transaction complete.');
            }

            $message = $response['message'] ?? 'Something went wrong.';
            return redirect()->route('orders.index')->with('error', $message);
        } catch (\Throwable $e) {
            return redirect()->route('orders.index')->with('error', 'Payment capture failed: ' . $e->getMessage());
        }
    }
}

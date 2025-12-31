<?php

namespace App\Http\Controllers;

use App\Http\Services\PayPalService;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    private PayPalService $paypalService;

    public function __construct()
    {
        $this->paypalService = new PayPalService();
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function paymentCancel(Request $request)
    {
        try {
            $token = $request->query('token');
            if (!$token) {
                return redirect()->route('orders.index')->with('error', 'Missing payment token.');
            }
            $this->paypalService->paymentCancel($token);

            return redirect()
                ->route('orders.index')
                ->with('error', 'You have canceled the transaction.');
        } catch (\Throwable $th) {
            return redirect()
                ->route('orders.index')
                ->with('error', 'Payment cancellation failed: ' . $th->getMessage());
        }
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
            $orderId = $this->paypalService->paymentSuccess($token);
            if ($orderId) {
                return redirect()->route('orders.show', ['orderId' => $orderId])->with('success', 'Transaction complete.');
            }

            return redirect()->route('orders.index')->with('success', 'Transaction complete.');
        } catch (\Throwable $e) {
            return redirect()->route('orders.index')->with('error', 'Payment capture failed: ' . $e->getMessage());
        }
    }
}

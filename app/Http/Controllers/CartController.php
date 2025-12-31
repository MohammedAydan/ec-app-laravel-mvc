<?php

namespace App\Http\Controllers;

use App\Http\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private CartService $cartService;

    public function __construct()
    {
        $this->cartService = new CartService();
    }

    public function index(Request $request)
    {
        try {
            $cartItems = $this->cartService->getMyCartItems($request->user()->id);
            return view('cart.index', ['cartItems' => $cartItems]);
        } catch (\Throwable $th) {
            return redirect()->back()
                ->withErrors('Could not load cart items at this time.')
                ->with('error', $th->getMessage());
        }
    }

    public function store(Request $request, $itemId, $quantity)
    {
        try {
            $userId = $request->user()->id;

            if ($userId == null) {
                return redirect()->route('login');
            }

            $this->cartService->create($userId, $itemId, $quantity);
            return redirect()->route('store.cart');
        } catch (\Throwable $th) {
            return redirect()->back()
                ->withErrors('Could not add item to cart at this time.')
                ->with('error', $th->getMessage());
        }
    }

    public function destroy(Request $request, $cartItemId)
    {
        try {
            $userId = $request->user()->id;

            $this->cartService->delete($cartItemId, $userId);
            return redirect()->route('store.cart');
        } catch (\Throwable $th) {
            return redirect()->back()
                ->withErrors('Could not delete cart item at this time.')
                ->with('error', $th->getMessage());
        }
    }

    public function incrementItemQuantity(Request $request, $cartItemId)
    {
        try {
            $userId = $request->user()->id;

            $this->cartService->incrementItemQuantity($cartItemId, $userId);
            return redirect()->route('store.cart');
        } catch (\Throwable $th) {
            return redirect()->back()
                ->withErrors('Could not increment cart item quantity at this time.')
                ->with('error', $th->getMessage());
        }
    }

    public function decrementItemQuantity(Request $request, $cartItemId)
    {
        try {
            $userId = $request->user()->id;

            $this->cartService->decrementItemQuantity($cartItemId, $userId);
            return redirect()->route('store.cart');
        } catch (\Throwable $th) {
            return redirect()->back()
                ->withErrors('Could not decrement cart item quantity at this time.')
                ->with('error', $th->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = Cart::where('user_id', $request->user()->id)->with('item')->get();
        return view('cart.index', ['cartItems' => $cartItems]);
    }

    public function store(Request $request, $itemId, $quantity)
    {
        $userId = $request->user()->id;

        if ($userId == null) {
            return redirect()->route('login');
        }

        $item = Cart::where('user_id', $userId)->where('item_id', $itemId)->first();

        if ($item != null) {
            $item->quantity += $quantity;
            $item->save();

            return redirect()->route('store.cart');
        }

        Cart::create([
            'user_id' => $userId,
            'item_id' => $itemId,
            'quantity' => $quantity,
        ]);

        return redirect()->route('store.cart');
    }

    public function destroy(Request $request, $cartItemId)
    {
        $userId = $request->user()->id;

        $cartItem = Cart::where('id', $cartItemId)->where('user_id', $userId)->first();

        if (!$cartItem) {
            abort(404, 'Cart item not found');
        }

        $cartItem->delete();

        return redirect()->route('store.cart');
    }

    public function incrementItemQuantity(Request $request, $cartItemId)
    {
        $userId = $request->user()->id;

        $cartItem = Cart::where('id', $cartItemId)->where('user_id', $userId)->first();

        if (!$cartItem) {
            abort(404, 'Cart item not found');
        }

        $cartItem->quantity += 1;
        $cartItem->save();

        return redirect()->route('store.cart');
    }

    public function decrementItemQuantity(Request $request, $cartItemId)
    {
        $userId = $request->user()->id;

        $cartItem = Cart::where('id', $cartItemId)->where('user_id', $userId)->first();

        if (!$cartItem) {
            abort(404, 'Cart item not found');
        }

        if ($cartItem->quantity > 1) {
            $cartItem->quantity -= 1;
            $cartItem->save();
        } else {
            $cartItem->delete();
        }

        return redirect()->route('store.cart');
    }
}

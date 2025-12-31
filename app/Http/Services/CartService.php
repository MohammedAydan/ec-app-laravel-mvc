<?php

namespace App\Http\Services;

use App\Models\Cart;
use Illuminate\Support\Facades\Log;

class CartService
{
    public function getMyCartItems($userId)
    {
        try {
            return Cart::where('user_id', $userId)->with('item')->get();
        } catch (\Throwable $th) {
            Log::error('Error fetching cart items', [
                'message' => $th->getMessage()
            ]);

            throw new \Exception('Could not fetch cart items at this time.');
        }
    }

    public function create($userId, $itemId, $quantity)
    {
        try {
            $item = Cart::where('user_id', $userId)->where('item_id', $itemId)->first();

            if ($item != null) {
                $item->quantity += $quantity;
                $item->save();

                return;
            }

            Cart::create([
                'user_id' => $userId,
                'item_id' => $itemId,
                'quantity' => $quantity,
            ]);
        } catch (\Throwable $th) {
            Log::error('Failed to create cart item.', [
                'message' => $th->getMessage()
            ]);

            throw new \Exception('Could not add item to cart at this time.');
        }
    }

    public function delete($cartItemId, $userId)
    {
        try {
            $cartItem = Cart::where('id', $cartItemId)->where('user_id', $userId)->first();

            if (!$cartItem) {
                throw new \Exception('Cart item not found.');
            }

            $cartItem->delete();
        } catch (\Throwable $th) {
            Log::error('Failed to delete cart item.', [
                'message' => $th->getMessage()
            ]);

            throw new \Exception('Could not delete cart item at this time.');
        }
    }

    public function incrementItemQuantity($cartItemId, $userId)
    {
        try {
            $cartItem = Cart::where('id', $cartItemId)->where('user_id', $userId)->first();

            if (!$cartItem) {
                throw new \Exception('Cart item not found.');
            }

            $cartItem->quantity += 1;
            $cartItem->save();
        } catch (\Throwable $th) {
            Log::error('Failed to increment cart item quantity.', [
                'message' => $th->getMessage()
            ]);

            throw new \Exception('Could not increment cart item quantity at this time.');
        }
    }

    public function decrementItemQuantity($cartItemId, $userId)
    {
        try {
            $cartItem = Cart::where('id', $cartItemId)->where('user_id', $userId)->first();

            if (!$cartItem) {
                throw new \Exception('Cart item not found.');
            }

            if ($cartItem->quantity > 1) {
                $cartItem->quantity -= 1;
                $cartItem->save();
            } else {
                $cartItem->delete();
            }
        } catch (\Throwable $th) {
            Log::error('Failed to decrement cart item quantity.', [
                'message' => $th->getMessage()
            ]);

            throw new \Exception('Could not decrement cart item quantity at this time.');
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        // Get cart items from database for logged in user
        $cartItems = CartItem::where('user_id', auth()->id())->get();
        
        // Format cart items to match the old session format
        $cart = [];
        foreach ($cartItems as $item) {
            $itemKey = $item->product_id . '-' . ($item->flavor ?? 'default');
            $cart[$itemKey] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product_name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'flavor' => $item->flavor,
                'image' => $item->image,
            ];
        }
        
        return view('cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $itemKey = $request->product_id . '-' . ($request->flavor ?? 'default');
        
        // Check if item already exists in cart
        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->where('flavor', $request->flavor ?? null)
            ->first();
        
        if ($cartItem) {
            // Update quantity if item exists
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Create new cart item
            CartItem::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'product_name' => $request->product_name,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'flavor' => $request->flavor ?? null,
                'image' => $request->image ?? null,
            ]);
        }
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Item added to cart!');
    }

    public function update(Request $request)
    {
        $parts = explode('-', $request->item_key);
        $productId = $parts[0];
        $flavor = $parts[1] === 'default' ? null : $parts[1];
        
        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->where('flavor', $flavor)
            ->first();
        
        if ($cartItem) {
            $cartItem->quantity = max(1, $request->quantity);
            $cartItem->save();
        }
        
        return back()->with('success', 'Cart updated!');
    }

    public function remove(Request $request)
    {
        $parts = explode('-', $request->item_key);
        $productId = $parts[0];
        $flavor = $parts[1] === 'default' ? null : $parts[1];
        
        CartItem::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->where('flavor', $flavor)
            ->delete();
        
        return back()->with('success', 'Item removed from cart!');
    }

    public function clear()
    {
        CartItem::where('user_id', auth()->id())->delete();
        return back()->with('success', 'Cart cleared!');
    }
}

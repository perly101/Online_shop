<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function delete($id)
    {
        $order = auth()->user()->orders()->findOrFail($id);
        // Only allow deleting cancelled or completed orders
        if (!in_array($order->status, ['cancelled', 'completed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only completed or cancelled orders can be deleted.'
            ], 400);
        }
        $order->items()->delete();
        $order->delete();
        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully.'
        ]);
    }
    public function index()
    {
        $orders = auth()->user()->orders()->with('items')->latest()->get();
        return view('orders.index', compact('orders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'payment_method' => 'required|string',
        ]);

        // Support direct checkout (Buy Now) with direct_item JSON or regular cart checkout
        $directItemJson = $request->input('direct_item');
        $selected = $request->input('selected_items'); // CSV of cart item ids or empty

        $cartItems = collect();
        if ($directItemJson) {
            $directData = json_decode($directItemJson, true);
            // sanitize keys
            $directItem = [
                'product_id' => $directData['product_id'] ?? null,
                'product_name' => $directData['name'] ?? $directData['product_name'] ?? 'Item',
                'price' => isset($directData['price']) ? floatval($directData['price']) : 0,
                'quantity' => isset($directData['quantity']) ? intval($directData['quantity']) : 1,
                'flavor' => $directData['flavor'] ?? null,
            ];
        } elseif ($selected) {
            $selectedIds = array_filter(array_map('intval', explode(',', $selected)));
            $cartItems = CartItem::where('user_id', auth()->id())->whereIn('id', $selectedIds)->get();
            $directItem = null;
        } else {
            $cartItems = CartItem::where('user_id', auth()->id())->get();
            $directItem = null;
        }
        
        if ($cartItems->isEmpty() && empty($directItem)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty or selected items are missing!');
        }

        DB::beginTransaction();
        try {
            // Calculate total
            $total = 0;
            if (!empty($directItem)) {
                $total = $directItem['price'] * $directItem['quantity'];
            } else {
                foreach ($cartItems as $item) {
                    $total += $item->price * $item->quantity;
                }
            }

            // Generate order number
            $orderNumber = 'AET-' . date('Ymd') . '-' . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT);

            // Generate unique QR code data (order number + verification code)
            $pickupQrCode = $orderNumber . '|' . bin2hex(random_bytes(8));

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => auth()->id(),
                'customer_name' => $validated['name'],
                'customer_mobile' => $validated['mobile'],
                'customer_email' => $validated['email'],
                'payment_method' => $validated['payment_method'],
                'total_amount' => $total,
                'status' => 'pending',
                'pickup_qr_code' => $pickupQrCode,
            ]);

            if (!empty($directItem)) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $directItem['product_id'],
                    'product_name' => $directItem['product_name'],
                    'price' => $directItem['price'],
                    'quantity' => $directItem['quantity'],
                    'flavor' => $directItem['flavor'] ?? null,
                ]);
            } else {
                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'flavor' => $item->flavor ?? null,
                    ]);
                }

                // Clear the ordered items from database (only those processed)
                $processedIds = $cartItems->pluck('id')->toArray();
                CartItem::where('user_id', auth()->id())->whereIn('id', $processedIds)->delete();
            }

            DB::commit();
            return redirect()->route('order.confirm', $order->id)->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cart.index')->with('error', 'Failed to place order. Please try again.');
        }
    }

    public function show($id)
    {
        $order = auth()->user()->orders()->with('items')->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    public function confirm($id)
    {
        $order = auth()->user()->orders()->with('items')->findOrFail($id);
        return view('orders.confirm', compact('order'));
    }

    public function cancel($id)
    {
        $order = auth()->user()->orders()->findOrFail($id);
        
        // Only allow cancellation of pending or processing orders
        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be cancelled.'
            ], 400);
        }
        
        $order->status = 'cancelled';
        $order->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully.'
        ]);
    }

    public function reorder($id)
    {
        $order = auth()->user()->orders()->with('items')->findOrFail($id);

        // Only allow reorder for completed orders (UI already restricts, but check)
        if ($order->status !== 'completed') {
            return response()->json(['success' => false, 'message' => 'Only completed orders can be reordered.'], 400);
        }

        // Add each order item to cart (increase quantity if exists)
        foreach ($order->items as $item) {
            $cartItem = CartItem::where('user_id', auth()->id())
                ->where('product_id', $item->product_id)
                ->where('flavor', $item->flavor ?? null)
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $item->quantity;
                $cartItem->save();
            } else {
                CartItem::create([
                    'user_id' => auth()->id(),
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'flavor' => $item->flavor ?? null,
                    'image' => optional($item->product)->image ?? null,
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Items added to cart.']);
    }
}

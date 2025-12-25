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

        // Get cart from database (optionally only selected items)
        $selected = $request->input('selected_items'); // CSV of cart item ids or empty
        if ($selected) {
            $selectedIds = array_filter(array_map('intval', explode(',', $selected)));
            $cartItems = CartItem::where('user_id', auth()->id())->whereIn('id', $selectedIds)->get();
        } else {
            $cartItems = CartItem::where('user_id', auth()->id())->get();
        }
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty or selected items are missing!');
        }

        DB::beginTransaction();
        try {
            // Calculate total
            $total = 0;
            foreach ($cartItems as $item) {
                $total += $item->price * $item->quantity;
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
}

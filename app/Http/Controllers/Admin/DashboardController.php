<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $orders = Order::with('user', 'items')->latest()->get();
        return view('admin.dashboard', compact('orders'));
    }

    public function orderHistory(Request $request)
    {
        $search = $request->input('search');
        
        $orders = Order::with('user', 'items')
            ->when($search, function ($query, $search) {
                return $query->where('order_number', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(20);
        
        return view('admin.order-history', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('user', 'items.product')->findOrFail($id);
        return response()->json($order);
    }

    public function verify(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'completed']);
        
        return response()->json(['success' => true]);
    }

    public function scanQr(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $order = Order::where('pickup_qr_code', $request->qr_code)->with('user', 'items')->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code. Order not found.'
            ], 404);
        }

        if ($order->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'This order has already been picked up.',
                'order' => $order
            ], 400);
        }

        if ($order->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'This order has been cancelled.',
                'order' => $order
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order found successfully!',
            'order' => $order
        ]);
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'message' => 'required|string',
            'type' => 'required|in:success,info,warning,error',
        ]);

        $order = Order::with('user')->findOrFail($request->order_id);

        // Create notification for the customer
        $notification = Notification::create([
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'message' => $request->message,
            'type' => $request->type,
            'read' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification sent successfully',
            'notification' => $notification
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,ready,completed,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = $request->status;
        
        $order->update(['status' => $newStatus]);

        // Auto-send notification for status changes
        $messageMap = [
            'ready' => "Your order #{$order->order_number} is ready for pickup! Please proceed to the store.",
            'processing' => "Your order #{$order->order_number} is now being processed.",
            'completed' => "Your order #{$order->order_number} has been successfully completed!",
            'cancelled' => "Your order #{$order->order_number} has been cancelled.",
            'pending' => "Your order #{$order->order_number} is pending."
        ];

        $typeMap = [
            'ready' => 'success',
            'completed' => 'success',
            'processing' => 'info',
            'cancelled' => 'warning',
            'pending' => 'info'
        ];

        if (isset($messageMap[$newStatus])) {
            Notification::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'message' => $messageMap[$newStatus],
                'type' => $typeMap[$newStatus] ?? 'info',
                'read' => false
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'order' => $order->load('user', 'items')
        ]);
    }
}

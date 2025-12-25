<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Check if it's an AJAX request
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'notifications' => $notifications->map(function($notif) {
                    return [
                        'id' => $notif->id,
                        'message' => $notif->message,
                        'type' => $notif->type,
                        'read' => $notif->read,
                        'time_ago' => $notif->created_at->diffForHumans(),
                        'created_at' => $notif->created_at->toIso8601String()
                    ];
                })
            ]);
        }
            
        return view('notifications.index', compact('notifications'));
    }

    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->count();
            
        return response()->json(['count' => $count]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);
            
        $notification->update(['read' => true]);
        
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->update(['read' => true]);
            
        return response()->json(['success' => true]);
    }

    public function deleteAll()
    {
        Notification::where('user_id', Auth::id())->delete();
        
        return response()->json(['success' => true]);
    }
}

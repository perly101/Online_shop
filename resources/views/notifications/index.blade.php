@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container" style="max-width: 800px; margin: 50px auto; padding: 20px;">
    <div class="notifications-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; font-size: 28px;"><i class="fas fa-bell"></i> Notifications</h2>
        <div style="display: flex; gap: 10px;">
            @if($notifications->where('read', false)->count() > 0)
                <button onclick="markAllAsRead()" class="btn-mark-all" style="background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    Mark All Read
                </button>
            @endif
            @if($notifications->count() > 0)
                <button onclick="deleteAllNotifications()" class="btn-delete-all" style="background: #e74c3c; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    Delete All
                </button>
            @endif
        </div>
    </div>

    @if($notifications->count() > 0)
        <div class="notifications-list">
            @foreach($notifications as $notification)
                <div class="notification-item {{ !$notification->read ? 'unread' : '' }}" 
                     style="background: {{ !$notification->read ? '#f0f7ff' : 'white' }}; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; margin-bottom: 15px; position: relative;">
                    
                    @if(!$notification->read)
                        <div class="unread-dot" style="position: absolute; top: 20px; right: 20px; width: 10px; height: 10px; background: #3498db; border-radius: 50%;"></div>
                    @endif
                    
                    <div style="display: flex; align-items: start; gap: 15px;">
                        <div class="notification-icon" style="width: 40px; height: 40px; border-radius: 50%; background: 
                            {{ $notification->type === 'success' ? '#27ae60' : 
                               ($notification->type === 'warning' ? '#f39c12' : 
                               ($notification->type === 'error' ? '#e74c3c' : '#3498db')) }}; 
                            display: flex; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                            <i class="fas fa-{{ $notification->type === 'success' ? 'check' : ($notification->type === 'warning' ? 'exclamation' : 'info') }}"></i>
                        </div>
                        
                        <div style="flex: 1;">
                            <p style="margin: 0 0 8px; font-size: 16px; color: #333;">
                                {{ $notification->message }}
                            </p>
                            <div style="font-size: 13px; color: #999;">
                                <i class="fas fa-clock"></i> <span class="local-time" data-ts="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        
                        @if(!$notification->read)
                            <button onclick="markAsRead({{ $notification->id }})" 
                                    style="background: #3498db; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-size: 13px; white-space: nowrap;">
                                Mark Read
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="no-notifications" style="text-align: center; padding: 60px 20px; color: #999;">
            <i class="fas fa-inbox" style="font-size: 64px; margin-bottom: 20px; color: #ddd;"></i>
            <p style="font-size: 18px;">No notifications yet</p>
        </div>
    @endif
</div>

<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function markAllAsRead() {
    if (confirm('Mark all notifications as read?')) {
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function deleteAllNotifications() {
    if (confirm('Delete all notifications? This cannot be undone.')) {
        fetch('/notifications/delete-all', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}
</script>
@endsection

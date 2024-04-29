<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        // Fetch notifications for the logged-in user
        $notifications = Notification::where('user_id', Auth::id())->get();

        // Display the notifications view with the fetched notifications
        return view('components.layout', ['notifications' => $notifications]);
    }

    public function markAsRead($id)
    {
        // Find the notification by ID
        $notification = Notification::findOrFail($id);

        // Mark the notification as read
        $notification->update(['read_at' => now()]);

        // Redirect back to the notifications index page
        return redirect()->route('notifications.index')->with('success', 'Notification marked as read.');
    }

    // Other methods for deleting notifications, etc.
}

<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);


        $notification->update(['read_at' => now()]);


        return redirect()->back()->with('success', 'Notification marked as read.');
    }


}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;

class NotificationUiController extends Controller
{
    public function clearAll()
    {
        UserNotification::where('user_id', Auth::id())->delete();

        return back()->with('success', 'Notifications vidées.');
    }

    public function markAllRead()
    {
        UserNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return back()->with('success', 'Notifications marquées comme lues.');
    }
}

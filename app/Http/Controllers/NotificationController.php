<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        return view('notifications.index', [
            'notifications' => Notification::query()
                ->where('user_id', $request->user()->id)
                ->latest()
                ->paginate(20),
        ]);
    }

    public function markSent(Request $request, Notification $notification): RedirectResponse
    {
        abort_unless($request->user()->isAdmin() || $notification->user_id === $request->user()->id, 403);

        $notification->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return back()->with('status', 'Notification marked as sent.');
    }
}

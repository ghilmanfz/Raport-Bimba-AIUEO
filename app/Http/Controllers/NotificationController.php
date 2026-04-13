<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->latest()
            ->take(20)
            ->get();

        return response()->json([
            'notifications' => $notifications->map(function ($n) {
                return [
                    'id'         => $n->id,
                    'type'       => $n->type,
                    'icon'       => $n->icon,
                    'title'      => $n->title,
                    'message'    => $n->message,
                    'link'       => $n->link,
                    'read'       => $n->isRead(),
                    'time'       => $n->created_at->diffForHumans(),
                    'created_at' => $n->created_at->toISOString(),
                ];
            }),
            'unread_count' => Notification::where('user_id', $request->user()->id)
                ->whereNull('read_at')
                ->count(),
        ]);
    }

    public function markAsRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            abort(403);
        }

        $notification->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}

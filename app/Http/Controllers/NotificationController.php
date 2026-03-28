<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function adminIndex(Request $request): JsonResponse
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        return $this->jsonResponseForUser(auth()->id());
    }

    public function userIndex(Request $request): JsonResponse|View
    {
        if ($request->expectsJson() || $request->ajax()) {
            return $this->jsonResponseForUser(auth()->id());
        }

        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('user.notifications.index', compact('notifications'));
    }

    public function markAllRead(): JsonResponse|RedirectResponse
    {
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    protected function jsonResponseForUser(int $userId): JsonResponse
    {
        $notifications = Notification::where('user_id', $userId)
            ->latest()
            ->take(20)
            ->get()
            ->map(function (Notification $notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'description' => $notification->description,
                    'order_id' => $notification->order_id,
                    'created_at' => optional($notification->created_at)->toIso8601String(),
                    'read_at' => optional($notification->read_at)->toIso8601String(),
                ];
            })
            ->values();

        $unreadCount = Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }
}

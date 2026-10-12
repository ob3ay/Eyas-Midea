<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $notifications = $user->notifications()
            ->latest('created_at')
            ->latest('id')
            ->limit(30)
            ->get();

        return response()->json([
            'notifications' => $notifications->map(fn (Notification $notification): array => $this->serialize($notification)),
            'unread_count' => $user->notifications()->unread()->count(),
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'unread_count' => $request->user()->notifications()->unread()->count(),
        ]);
    }

    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        $this->assertRecipient($request, $notification);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => $request->user()->notifications()->unread()->count(),
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->notifications()
            ->unread()
            ->update([
                'read_at' => now(),
                'isread' => true,
            ]);

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    private function assertRecipient(Request $request, Notification $notification): void
    {
        abort_unless(
            (int) $notification->recipient_user_id === (int) $request->user()->id,
            404,
        );
    }

    private function serialize(Notification $notification): array
    {
        return [
            'id' => $notification->id,
            'type' => $notification->type,
            'title' => $notification->title,
            'message' => $notification->message,
            'read' => $notification->isRead(),
            'created_at' => $notification->created_at?->toISOString(),
            'url' => $this->relatedEntityUrl($notification),
        ];
    }

    private function relatedEntityUrl(Notification $notification): ?string
    {
        if (Str::endsWith($notification->related_entity_type ?? '', '\\Task') && $notification->related_entity_id !== null) {
            return route('tasks.index').'#task-'.$notification->related_entity_id;
        }

        if (Str::endsWith($notification->related_entity_type ?? '', '\\Project') && $notification->related_entity_id !== null) {
            return route('projects.index').'#project-'.$notification->related_entity_id;
        }

        return null;
    }
}

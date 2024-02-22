<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\Scan;
use App\Services\UserNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    private UserNotificationService $notificationService;

    public function __construct()
    {
        $this->notificationService = new UserNotificationService();
    }

    public function index(Request $request, string $type): AnonymousResourceCollection
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->where('data->type', $type)
            ->latest()
            ->paginate(10);

        return NotificationResource::collection($notifications);
    }

    public function isNew(Request $request): JsonResponse
    {
        $user = $request->user();
        $hasUnreadNotifications = $user->unreadNotifications()->exists();
        return response()->json(['data' => ['isNew' => $hasUnreadNotifications]]);
    }

    public function markAsRead(Request $request): JsonResponse
    {
        $user = $request->user();

        $notificationIds = $request->input('ids', []); // 'ids' is an array of notification IDs
        if ($user->notifications()
            ->whereIn('id', $notificationIds)
            ->update(['read_at' => now()])) {
            Log::info($request->get('ids'));
        }

        return response()->json(['data' => 'success']);
    }

    public function sendTestNotification(): array
    {
        $scan = Scan::findOrFail(2);
        $this->notificationService->notifyStatusChange($scan);
        return ['success'];
    }

}

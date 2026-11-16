<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Display admin notification history.
     */
    public function adminIndex(Request $request): View
    {
        return $this->renderIndex($request, 'admin.notifications.index');
    }

    /**
     * Display instructor notification history.
     */
    public function instructorIndex(Request $request): View
    {
        return $this->renderIndex($request, 'instructor.notifications.index');
    }

    /**
     * Display student notification history.
     */
    public function studentIndex(Request $request): View
    {
        return $this->renderIndex($request, 'student.notifications.index');
    }

    /**
     * Common method to paginate and filter user notifications.
     */
    protected function renderIndex(Request $request, string $viewName): View
    {
        $user = $request->user();
        $status = (string) $request->query('status', 'all');

        $query = $user->notifications();

        if ($status === 'unread') {
            $query->whereNull('read_at');
        } elseif ($status === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => $user->notifications()->count(),
            'unread' => $user->unreadNotifications()->count(),
            'read' => $user->notifications()->whereNotNull('read_at')->count(),
        ];

        return view($viewName, [
            'notifications' => $notifications,
            'stats' => $stats,
            'activeStatus' => $status,
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        // Enforce strict user ownership
        $notification = $user->notifications()->where('id', $id)->first();

        if (! $notification) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Notification not found or access denied.'], 404);
            }
            abort(404, 'Notification not found.');
        }

        if ($notification->unread()) {
            $notification->markAsRead();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all unread notifications of the authenticated user as read.
     */
    public function markAllAsRead(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $this->notificationService->markAllAsRead($user);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Mark notification as read and redirect to the target URL.
     */
    public function readAndRedirect(Request $request, string $id): RedirectResponse
    {
        $user = $request->user();

        $notification = $user->notifications()->where('id', $id)->first();

        if (! $notification) {
            abort(404, 'Notification not found.');
        }

        if ($notification->unread()) {
            $notification->markAsRead();
        }

        $targetUrl = $notification->data['url'] ?? null;

        if ($targetUrl && is_string($targetUrl)) {
            // Prevent open redirect vulnerabilities: ensure relative or same-host URL
            $appUrl = config('app.url');
            $isSameHost = str_starts_with($targetUrl, '/') || (str_starts_with($targetUrl, $appUrl));

            if ($isSameHost) {
                return redirect()->to($targetUrl);
            }
        }

        // Fallback destination based on role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isInstructor()) {
            return redirect()->route('instructor.dashboard');
        }

        return redirect()->route('student.dashboard');
    }
}

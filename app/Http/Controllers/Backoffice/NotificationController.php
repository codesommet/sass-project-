<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{

    /**
     * Display active notifications.
     */
    public function index(Request $request)
    {
        $userId = Auth::guard('backoffice')->id();

        $query = Notification::where('user_id', $userId)
            ->active()
            ->orderBy('created_at', 'desc');

        // Filter by read/unread
        if ($request->filled('filter')) {
            if ($request->filter === 'unread') {
                $query->unread();
            } elseif ($request->filter === 'read') {
                $query->read();
            }
        }

        $notifications = $query->paginate(15)->withQueryString();

        return view('backoffice.notifications.index', compact('notifications'));
    }

    /**
     * Display archived notifications.
     */
    public function archived(Request $request)
    {
        $userId = Auth::guard('backoffice')->id();

        $notifications = Notification::where('user_id', $userId)
            ->archived()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('backoffice.notifications.archived', compact('notifications'));
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        $userId = Auth::guard('backoffice')->id();

        if ($notification->user_id !== $userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $userId = Auth::guard('backoffice')->id();

        Notification::where('user_id', $userId)
            ->unread()
            ->update(['is_read' => true]);

        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    }

    /**
     * Archive notification.
     */
    public function archive(Notification $notification)
    {
        $userId = Auth::guard('backoffice')->id();

        if ($notification->user_id !== $userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $notification->update(['is_archived' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Archive all read notifications.
     */
    public function archiveAllRead()
    {
        $userId = Auth::guard('backoffice')->id();

        Notification::where('user_id', $userId)
            ->read()
            ->update(['is_archived' => true]);

        return response()->json(['success' => true, 'message' => 'All read notifications archived']);
    }

    /**
     * Clear all notifications (archive all).
     */
    public function clearAll()
    {
        $userId = Auth::guard('backoffice')->id();

        Notification::where('user_id', $userId)
            ->active()
            ->update(['is_archived' => true]);

        return response()->json(['success' => true, 'message' => 'All notifications cleared']);
    }

    /**
     * Delete notification permanently.
     */
    public function destroy(Notification $notification)
    {
        $userId = Auth::guard('backoffice')->id();

        if ($notification->user_id !== $userId) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            return redirect()->back()->with('toast', [
                'title' => 'Erreur',
                'message' => 'Vous n\'êtes pas autorisé à supprimer cette notification.',
                'dot' => '#dc3545',
                'delay' => 3500,
                'time' => 'now',
            ]);
        }

        $notification->delete();

        // If it's an AJAX request, return JSON
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        // Otherwise redirect back
        return redirect()->back()->with('toast', [
            'title' => 'Supprimé',
            'message' => 'Notification supprimée avec succès.',
            'dot' => '#dc3545',
            'delay' => 3500,
            'time' => 'now',
        ]);
    }

    /**
     * Get unread count for header.
     */
    public function getUnreadCount()
    {
        $userId = Auth::guard('backoffice')->id();

        $count = Notification::where('user_id', $userId)
            ->unread()
            ->count();

        $activeCount = Notification::where('user_id', $userId)
            ->active()
            ->count();

        $archivedCount = Notification::where('user_id', $userId)
            ->archived()
            ->count();

        return response()->json([
            'unread' => $count,
            'active' => $activeCount,
            'archived' => $archivedCount
        ]);
    }

    /**
     * Delete all archived notifications.
     */
    public function deleteAllArchived()
    {
        $userId = Auth::guard('backoffice')->id();

        try {
            DB::beginTransaction();
            
            Notification::where('user_id', $userId)
                ->archived()
                ->delete();
                
            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
 * Delete all active notifications.
 */
public function deleteAll()
{
    $userId = Auth::guard('backoffice')->id();

    try {
        DB::beginTransaction();
        
        Notification::where('user_id', $userId)
            ->active()
            ->delete();
            
        DB::commit();

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

    /**
     * Get recent notifications for header dropdown.
     */
    public function getRecent()
    {
        $userId = Auth::guard('backoffice')->id();

        $active = Notification::where('user_id', $userId)
            ->active()
            ->with('performer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'link' => $notification->link,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'is_read' => $notification->is_read,
                    'formatted_time' => $notification->formatted_time,
                    'performer_name' => $notification->performer_name,
                ];
            });

        $unread = Notification::where('user_id', $userId)
            ->unread()
            ->with('performer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'link' => $notification->link,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'is_read' => $notification->is_read,
                    'formatted_time' => $notification->formatted_time,
                    'performer_name' => $notification->performer_name,
                ];
            });

        $archived = Notification::where('user_id', $userId)
            ->archived()
            ->with('performer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'link' => $notification->link,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'is_read' => $notification->is_read,
                    'formatted_time' => $notification->formatted_time,
                    'performer_name' => $notification->performer_name,
                ];
            });

        $counts = [
            'unread' => Notification::where('user_id', $userId)->unread()->count(),
            'active' => Notification::where('user_id', $userId)->active()->count(),
            'archived' => Notification::where('user_id', $userId)->archived()->count(),
        ];

        return response()->json([
            'active' => $active,
            'unread' => $unread,
            'archived' => $archived,
            'counts' => $counts
        ]);
    }
}
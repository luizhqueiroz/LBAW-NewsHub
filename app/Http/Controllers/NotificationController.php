<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\News;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('notification_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('pages.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->update(['viewed' => true]);

        return redirect()->route('notifications.index')->with('success', 'Notification marked as read.');
    }

    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return redirect()->route('notifications.index')->with('success', 'Notification deleted.');
    }

    public function userData()
    {
        $user = auth()->user();

        // Fetch IDs of news the user authored or commented on
        $newsIds = News::where('author_id', $user->id)
            ->orWhereHas('comments', function ($query) use ($user) {
                $query->where('author_id', $user->id);
            })
            ->pluck('id');

        return response()->json([
            'newsIds' => $newsIds,
            'userId' => $user->id,
        ]);
    }
}

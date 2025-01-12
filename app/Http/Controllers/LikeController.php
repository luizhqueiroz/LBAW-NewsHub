<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Events\LikeNotification;

class LikeController extends Controller
{
    public function toggleNewsLike(Request $request, $newsId) {
        $user = Auth::user();

        $news = News::findOrFail($newsId);

        $like = $news->likes()->where('sender_id', $user->id)->first();
    
        if ($like) {
            $like->delete();
            return response()->json(['message' => 'Disliked', 'likes_count' => $news->likes()->count()]);
        } else {
            $like = $news->likes()->create(['sender_id' => $user->id]);

            event(new LikeNotification($like->load('sender')));

            return response()->json(['message' => 'Liked', 'likes_count' => $news->likes()->count()]);
        }
    }

    public function toggleCommentLike(Request $request, $commentId) {
        $user = Auth::user();

        $comment = Comment::findOrFail($commentId);

        $like = $comment->likes()->where('sender_id', $user->id)->first();
    
        if ($like) {
            $like->delete();
            return response()->json(['message' => 'Disliked', 'likes_count' => $comment->likes()->count()]);
        } else {
            $like = $comment->likes()->create(['sender_id' => $user->id]);
            
            event(new LikeNotification($like->load(['comment', 'sender'])));

            return response()->json(['message' => 'Liked', 'likes_count' => $comment->likes()->count()]);
        }
    }
}

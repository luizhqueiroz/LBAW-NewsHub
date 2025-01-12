<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Events\CommentNotification;

class CommentController extends Controller
{
    public function store(Request $request, $newsId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = new Comment();
        $comment->news_id = $newsId;
        $comment->author_id = Auth::user()->id;
        $comment->published_date = now();
        $comment->content = $request->content;

        $comment->save();

        $comment->likes_count = 0;

        event(new CommentNotification($comment));

        if ($request->ajax()) {
            $commentHtml = view('partials.comment', compact('comment'))->render();
            
            return response()->json([
                'success' => true,
                'commentHtml' => $commentHtml,
            ]);
     
        } else {
            return redirect()->route('news.show', ['id' => $newsId]);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = Comment::find($id);

        $this->authorize('update', $comment);

        $comment->content = $request->content;

        $comment->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
            ]);
        }

        return redirect()->route('news.show', ['id' => $comment->news_id]);
    }

    public function destroy($id)
    {
        $comment = Comment::find($id);

        $this->authorize('destroy', $comment);

        $newsId = $comment->news_id;
        $comment->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
            ]);
        }

        return redirect()->route('news.show', ['id' => $newsId]);
    }
}

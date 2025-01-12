<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\News;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function searchUser(Request $request) 
    {
        $query = $request->input('query');

        $users = User::with('image')->where('user_name', 'like', '%' . $query . '%')->get();
        
        if ($request->ajax()) {
            if ($request->expectsJson()) {
                return response()->json($users);
            }
            
            $html = $users->map(function ($item) {
                return view('partials.user', ['user' => $item])->render();
            })->implode('');

            return response()->json([
                'html' => $html,
            ]);
        }
        
        return view('pages.search.users', ['users' => $users, 'searchQuery' => $query]);
    }

    public function searchNews(Request $request)
    {   
        if (Auth::anyCheck()) {
            $news = News::whereRaw("ts_content_user @@ plainto_tsquery(?)", [$request->input('query')])
                ->with('author')
                ->with('tags')
                ->withCount(['likes', 'comments'])
                ->orderBy('published_date', 'desc')
                ->get();
        } else {
            $news = News::whereRaw("ts_content_user @@ plainto_tsquery(?)", [$request->input('query')])
                ->withCount('comments')
                ->orderBy('published_date', 'desc')
                ->get();
        }

        if ($request->expectsJson()) {
            return response()->json($news);
        }

        $html = $news->map(function ($item) {
            return view('partials.news', ['news' => $item, 'isNewsItem' => false])->render();
        })->implode('');

        return response()->json([
            'html' => $html,
        ]);
    }

    public function searchComments(Request $request)
    {
        if (Auth::anyCheck()) {
            $comments = Comment::whereRaw("ts_content_user @@ plainto_tsquery(?)", [$request->input('query')])
                ->with('author')
                ->withCount('likes')
                ->orderBy('published_date', 'desc')
                ->get();
        } else {
            $comments = Comment::whereRaw("ts_content_user @@ plainto_tsquery(?)", [$request->input('query')])
                ->orderBy('published_date', 'desc')
                ->get();
        }

        if ($request->expectsJson()) {
            return response()->json($comments);
        }

        $html = $comments->map(function ($item) {
            return view('partials.comment', ['comment' => $item])->render();
        })->implode('');

        return response()->json([
            'html' => $html,
        ]);
    }
}

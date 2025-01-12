<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    public function index() {
        if (Auth::anyCheck()) {
            $news = News::with('author')
                ->with('tags')
                ->withCount(['likes', 'comments'])
                ->orderBy('likes_count', 'desc') 
                ->orderBy('published_date', 'desc')
                ->paginate(10);
        } else {
            $news = News::withCount(['likes', 'comments'])
                ->orderBy('likes_count', 'desc') 
                ->orderBy('published_date', 'desc')
                ->paginate(10);
        }

        return view('home', compact('news'));
    }

    public function getTopNews()
    {
        if (Auth::anyCheck()) {
            $news = News::with('author')
                ->with('tags')
                ->withCount(['likes', 'comments'])
                ->orderBy('likes_count', 'desc') 
                ->orderBy('published_date', 'desc')
                ->paginate(10);
        } else {
            $news = News::withCount(['likes', 'comments'])
                ->orderBy('likes_count', 'desc') 
                ->orderBy('published_date', 'desc')
                ->paginate(10);
        }

        $html = $news->map(function ($item) {
            return view('partials.news', ['news' => $item, 'isNewsItem' => false])->render();
        })->implode('');

        return $html;
    }

    public function getRecentNews()
    { 
        if (Auth::anyCheck()) {
            $news = News::with('author')
                ->with('tags')
                ->withCount(['likes', 'comments'])
                ->orderBy('published_date', 'desc')
                ->paginate(10);
        } else {
            $news = News::withCount('comments')
                ->orderBy('published_date', 'desc')
                ->paginate(10);
        }
       
        $html = $news->map(function ($item) {
            return view('partials.news', ['news' => $item, 'isNewsItem' => false])->render();
        })->implode('');

        return $html;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|max:255',
            'tmdb_id' => 'nullable|integer'
        ]);

        News::create([
            'content' => $request->content,
            'published_date' => now(),
            'author_id' => Auth::user()->id,
            'movie_id' => $request->tmdb_id
        ]);

        return redirect()->route('home')->with('success', 'News created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View 
    {   
        if (Auth::anyCheck()) {
            $news = News::with('author')
                ->with('comments.author')
                ->with('tags')
                ->withCount(['likes', 'comments'])
                ->with(['comments' => function($query) {
                    $query->withCount('likes');
                }])
                ->findOrFail($id);
        } else {
            $news = News::withCount('comments')
                ->findOrFail($id);
        }

        return view('pages.news.show', ['news' => $news]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {      
        $request->validate([
            'content' => 'required|max:255',
            'image' => 'nullable|image|max:2048'
        ]);

        $news = News::findOrFail($id);

        $this->authorize('update', $news);

        $news->update([
            'content' => $request->content
        ]);

        return response()->json($news);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $news = News::findOrFail($id);

            $this->authorize('delete', $news);
            
            $news->delete();
    
            if (request()->ajax()) {
                return response()->json(['status' => 'success', 'message' => 'News deleted successfully!']);
            }
    
            return redirect()->route('home')->with('success', 'News deleted successfully!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'News could not be deleted!']);
            }
    
            return redirect()->back()->with('error', 'News could not be deleted!');
        }
    }
}

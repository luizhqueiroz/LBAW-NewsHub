<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Auth;

class TagsController extends Controller
{

    public function showTags()
    {
        $tags = Tag::all(); // Fetch all tags
        $user = Auth::user(); // Get the authenticated user
        $news = []; // Default to an empty array if the user is not logged in

        // Join `ask_tag` with the `users` table to get user details
        $requests = \DB::table('ask_tag')
            ->join('users', 'ask_tag.user_id', '=', 'users.id')
            ->select('ask_tag.*', 'users.user_name') // Select required fields, including `user_name`
            ->get();

        if (Auth::guard('admin')->check()) {
            return view('pages.admins.tags', compact('tags', 'news', 'user', 'requests'));
        }

        if (auth()->check()) {
            // Get the IDs of the tags followed by the logged-in user
            $followedTags = auth()->user()->followedTags()->pluck('id');

            // Fetch news associated with the followed tags
            $news = News::whereHas('tags', function ($query) use ($followedTags) {
                $query->whereIn('tag.id', $followedTags);
            })->get();
        }

        return view('pages.tags.tags', compact('tags', 'news', 'user'));
    }



    public function createTag(Request $request)
    {
        $validated = $request->validate([
            'tag_name' => 'required|string|max:255|unique:tag,name',
        ]);

        Tag::create(['name' => $validated['tag_name']]);

        return redirect()->route('tags.show')->with('success', 'Tag created successfully!');
    }
    public function deleteTag($tagId)
    {
        $tag = Tag::find($tagId);

        if (!$tag) {
            return redirect()->route('tags.show')->with('error', 'Tag not found');
        }

        // Optional: Check if the tag is associated with any news items
        $associatedNewsCount = $tag->news()->count();
        if ($associatedNewsCount > 0) {
            return redirect()->route('tags.show')->with('error', 'Cannot delete tag associated with news items');
        }

        $tag->delete();

        return redirect()->route('tags.show')->with('success', 'Tag deleted successfully');
    }

    public function askTag(Request $request)
    {
        $validated = $request->validate([
            'tag_name' => 'required|string|max:255|unique:ask_tag,tag_name',
        ]);

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('tags.show')->with('error', 'You need to be logged in to request a tag.');
        }

        // Store the tag request in the `ask_tags` table
        \DB::table('ask_tag')->insert([
            'user_id' => $user->id,
            'tag_name' => $validated['tag_name'],
        ]);

        return redirect()->route('tags.show')->with('success', 'Your tag request has been submitted successfully.');
    }
    public function acceptTag($id)
    {
        $request = \DB::table('ask_tag')->find($id);

        if (!$request) {
            return redirect()->route('tags.show')->with('error', 'Tag request not found.');
        }

        // Create the tag
        Tag::create(['name' => $request->tag_name]);

        // Remove the request from the database
        \DB::table('ask_tag')->where('id', $id)->delete();

        return redirect()->route('tags.show')->with('success', 'Tag request accepted and tag created.');
    }

    public function rejectTag($id)
    {
        $request = \DB::table('ask_tag')->find($id);

        if (!$request) {
            return redirect()->route('tags.show')->with('error', 'Tag request not found.');
        }

        // Remove the request from the database
        \DB::table('ask_tag')->where('id', $id)->delete();

        return redirect()->route('tags.show')->with('success', 'Tag request rejected.');
    }


    public function toggleFollowTag($tagId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 403);
        }

        $tag = Tag::find($tagId);

        if (!$tag) {
            return response()->json(['success' => false, 'message' => 'Tag not found'], 404);
        }

        // Check if the user is already following the tag
        if ($user->followedTags()->where('tag_id', $tagId)->exists()) {
            // Unfollow the tag
            $user->followedTags()->detach($tagId);
            return response()->json(['success' => true, 'message' => 'Tag unfollowed successfully']);
        }

        // Follow the tag
        $user->followedTags()->attach($tagId);
        return response()->json(['success' => true, 'message' => 'Tag followed successfully']);
    }


}

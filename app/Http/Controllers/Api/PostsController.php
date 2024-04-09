<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Friend;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($limit = 5, $page = 1)
    {
        $authUserId = auth()->id();

        $friends = Friend::where(function ($query) use ($authUserId) {
            $query->where("owner_id", $authUserId)
                ->orWhere("user_id", $authUserId);
        })
            ->where("status", true)
            ->orderBy("created_at", "desc")
            ->get();

        $friendIds = $friends->pluck('owner_id')->merge($friends->pluck('user_id'));

        if ($friends->count() == 0) {
            $posts = Post::whereIn('owner_id', [$authUserId]) // Only include the authenticated user
                ->orderBy('created_at', 'desc')
                ->skip(($page - 1) * $limit)
                ->take($limit)
                ->with('user')
                ->with("sharing")
                ->withCount("sharings")
                ->withCount("likes")
                ->withCount("comments")
                ->withExists("isLike")
                ->get();
        } else {
            $posts = Post::whereIn('owner_id', $friendIds)
                ->orderBy('created_at', 'desc')
                ->orderBy('updated_at', 'desc')
                ->skip(($page - 1) * $limit)
                ->take($limit)
                ->with('user')
                ->with("sharing")
                ->withCount("sharings")
                ->withCount("likes")
                ->withCount("comments")
                ->withExists("isLike")
                ->get();
        }

        return response()->json($posts, 200);
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'title' => 'required',
        //     'body' => 'required',
        // ]);

        $post = new Post();
        $post->owner_id = auth()->id();
        $post->title = $request->title;
        $post->body = $request->body;
        if ($request->file("media_url")) {
            $file_extension = $request->file("media_url")->getClientOriginalExtension();
            $path = "Assets/Posts";
            $filename = time() . "-" . auth()->id() . "." . $file_extension;
            $request->file("media_url")->move($path, $filename);
            $post->media_type = $request->media_type;
            $post->media_url = $filename;
        }
        $post->sharing_post_id = $request->sharing_post_id;
        $post->save();


        $post = Post::where("id", $post->id)
            ->with('user')
            ->with("sharing")
            ->withCount("sharings")
            ->withCount("likes")
            ->withCount("comments")
            ->withExists("isLike")
            ->first();

        return response()->json($post, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($postId)
    {
        $post = Post::where("id", $postId)
            ->with('user')
            ->with("sharing")
            ->withCount("sharings")
            ->withCount("likes")
            ->withCount("comments")
            ->withExists("isLike")
            ->first();

        return response()->json($post, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        // $request->validate([
        //     'title' => 'required',
        //     'body' => 'required',
        // ]);

        $post->title = $request->title;
        $post->body = $request->body;

        $post->update();

        $post = Post::where('id', $post->id)
            ->with('user')
            ->with("sharing")
            ->withCount("sharings")
            ->withCount("likes")
            ->withCount("comments")
            ->withExists("isLike")
            ->first();

        return $post;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        try {
            Like::where("post_id", $post->id)->delete();
            Comment::where("post_id", $post->id)->delete();
            Post::where("sharing_post_id", $post->id)->delete();
            $post->delete();
            return response()->json(['message' => 'Post deleted successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error deleted'], 200);
        }
    }
}

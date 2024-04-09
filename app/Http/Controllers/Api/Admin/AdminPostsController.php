<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountConfirmation;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class AdminPostsController extends Controller
{
    public function index($limit = 10, $page = 1)
    {
        $posts = Post::orderBy("created_at", "desc")
            ->with('user')
            ->with("sharing")
            ->withCount("sharings")
            ->withCount("likes")
            ->withCount("comments")
            ->withCount('isLike')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        return response()->json($posts);
    }

    public function searchPosts($query_params)
    {
        $posts = Post::where(function ($query) use ($query_params) {
            $query->where("title", $query_params)
                ->orWhere("title", 'like', "%$query_params%")
                ->orWhere("body", 'like', "%$query_params%");
        })
            ->with('user')
            ->with("sharing")
            ->withCount("sharings")
            ->withCount("likes")
            ->withCount("comments")
            ->withCount('isLike')
            ->take(10)
            ->get();

        return response()->json($posts, 200);
    }

    public function searchById($query_params)
    {
        $posts = Post::where("id", $query_params)
            ->with('user')
            ->with("sharing")
            ->withCount("sharings")
            ->withCount("likes")
            ->withCount("comments")
            ->withCount('isLike')
            ->take(10)
            ->get();

        return response()->json($posts, 200);
    }

    public function searchByUserId($query_params)
    {
        $posts = Post::where("owner_id", $query_params)
            ->with('user')
            ->with("sharing")
            ->withCount("sharings")
            ->withCount("likes")
            ->withCount("comments")
            ->withCount('isLike')
            ->take(10)
            ->get();

        return response()->json($posts, 200);
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json(true, 200);
    }

    public function getAnlsAdmin()
    {
        $users = User::count();
        $posts = Post::count();
        $ra = AccountConfirmation::count();
        
        return response()->json([
            'users_count' => $users,
            'posts_count' => $posts,
            'accounts_confirmation_count' => $ra,
        ], 200);
    }
}

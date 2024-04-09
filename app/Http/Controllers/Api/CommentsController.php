<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($postId)
    {
        $comments = Comment::where("post_id", $postId)->orderBy("id", "desc")->with("user")->get();
        return response()->json($comments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            "post_id" => "required",
            "comment" => "required"
        ]);

        $comment = new Comment();
        $comment->post_id = $request->post_id;
        $comment->owner_id = auth()->user()->id;
        $comment->comment = $request->comment;
        $comment->save();

        $res = Comment::where("id", $comment->id)
            ->with("user")->first();

        return response()->json($res, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return response()->json([], 200);
    }
}

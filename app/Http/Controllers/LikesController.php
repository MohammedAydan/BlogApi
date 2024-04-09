<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;

class LikesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($postId)
    {
        $like = Like::where("post_id", $postId)->get();
        return response()->json($like, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "post_id" => 'required',
        ]);

        $isExists = Like::where("post_id", $request->post_id)->where("owner_id", auth()->user()->id)->exists();

        if ($isExists) {
            return;
        }

        $like = new Like();
        $like->owner_id = auth()->user()->id;
        $like->post_id = $request->post_id;
        $like->save();

        return response()->json($like, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Like $like)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Like $like)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($postId)
    {
        $isExists = Like::where("post_id", $postId)->where("owner_id", auth()->user()->id);

        if ($isExists->exists()) {
            $isExists->delete();

            return response()->json([], 200);
        }

        return response()->json([], 200);
    }
}

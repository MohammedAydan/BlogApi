<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sharing;
use Illuminate\Http\Request;

class SharingsController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            "post_id" => "required"
        ]);

        $sharing = new Sharing();
        $sharing->owner_id = auth()->id();
        $sharing->title = $request->title;
        $sharing->body = $request->body;
        $sharing->post_id = $request->post_id;
        $sharing->save();

        return response()->json([
            "message" => "Sharing created successfully",
            "sharing" => $sharing
        ], 201);
    }
}

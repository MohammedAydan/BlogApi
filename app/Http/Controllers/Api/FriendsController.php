<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use Illuminate\Http\Request;

class FriendsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authUserId = auth()->id();

        $friends = Friend::where(function ($query) use ($authUserId) {
            $query->where("owner_id", $authUserId)
                ->orWhere("user_id", $authUserId);
        })
            ->where("status", true)
            ->with("user")
            ->with("owner")
            ->get();

        return response()->json($friends, 200);
    }

    public function requests()
    {
        $authUserId = auth()->id();

        $friends = Friend::where("user_id", $authUserId)
            ->with("user")->with("owner")
            ->get();

        return response()->json($friends, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "user_id" => 'required',
        ]);

        $isReq = Friend::where("owner_id", auth()->id())->where("user_id", $request->user_id);

        if ($isReq->exists()) {
            return $isReq->first();
        }

        $isReq = Friend::where("user_id", auth()->id())->where("owner_id", $request->user_id);

        if ($isReq->exists()) {
            return $isReq->first();
        }

        $reqFr = new Friend();
        $reqFr->owner_id = auth()->id();
        $reqFr->user_id = $request->user_id;
        $reqFr->status = false;
        $reqFr->save();

        return response()->json([
            "status" => "success",
            "owner_id" => $reqFr->owner_id,
            "request" => $reqFr,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $is_req = Friend::where("owner_id", auth()->id())->where("user_id", $id);

        if ($is_req->exists()) {
            $is_req = $is_req->first();

            return response()->json([
                "status" => "success",
                "owner_id" => $is_req->owner_id,
                "request" => $is_req,
            ], 200);
        }

        $is_req = Friend::where("owner_id", $id)->where("user_id", auth()->id());

        if ($is_req->exists()) {
            $is_req = $is_req->first();

            return response()->json([
                "status" => "success",
                "owner_id" => $is_req->owner_id,
                "request" => $is_req,
            ], 200);
        }

        return response()->json([
            "status" => "success",
            "owner_id" => null,
            "request" => null,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Friend $friend)
    {
        $friend->status = true;
        $friend->update();

        return response()->json([
            "status" => "success",
            "owner_id" => $friend->owner_id,
            "request" => $friend,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Friend $friend)
    {
        $friend->delete();

        return response()->json([
            "status" => "success",
            "owner_id" => null,
            "request" => null,
        ], 200);
    }
}

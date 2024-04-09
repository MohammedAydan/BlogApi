<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPremission;
use Illuminate\Http\Request;

class PremissionsForUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "premission_id" => "required",
            "user_id" => "required",
        ]);

        $userPremission = new UserPremission();
        $userPremission->user_id = $request->user_id;
        $userPremission->premission_id = $request->premission_id;
        $userPremission->save();

        $newUserPremissions = User::find($request->user_id)->premissions;

        return response()->json($newUserPremissions, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserPremission $userPremission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserPremission $userPremission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($userId, $premissionId)
    {
        $userPremission = UserPremission::where("user_id", $userId)->where("premission_id", $premissionId)->first();
        $userPremission->delete();
        return response()->json(true, 200);
    }
}

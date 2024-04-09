<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index($limit = 1, $page = 1)
    {
        $users = User::orderBy("created_at", "desc")->with("premissions")->skip(($page - 1) * $limit)->take($limit)->get();
        return response()->json($users, 200);
    }
    public function getUser(Request $request)
    {
        $premissions = $request->user()->premissions->map(function ($premission) {
            return $premission->name;
        });

        return response()->json([
            ...$request->user()->toArray(),
            "premissions" => $premissions,
        ]);
    }

    public function show($userId)
    {
        $user = User::where("id", $userId)
            ->first();
            
        return response()->json($user, 200);
    }

    public function searchUsers($query_params)
    {
        $users = User::where("id", "!=", auth()->id())->where(function ($query) use ($query_params) {
            $query->where("username", $query_params)->orWhere("name", 'like', "%$query_params%")->orWhere("email", 'like', "%$query_params%");
        })
            ->with("premissions")
            ->take(10)
            ->get();

        return response()->json($users, 200);
    }

    public function searchUsersById($query_params)
    {
        $users = User::where("id", "!=", auth()->id())->where("id", $query_params)
            ->with("premissions")
            ->take(10)
            ->get();

        return response()->json($users, 200);
    }

    public function update(Request $request)
    {
        $user = User::find(auth()->id());
        $user->update($request->all());
        return response()->json($user, 200);
    }

    public function destroy()
    {
        $user = User::find(auth()->id());
        $user->deleted = true;
        $user->save();
        return response()->json($user, 200);
    }

    public function resetPassword(Request $request)
    {
        if (!$request->password || !$request->new_password) {
            return response()->json([
                "status" => false,
                "error" => "error with data",
            ], 200);
        }

        if (!Auth::guard('web')->attempt(['email' => auth()->user()->email, 'password' => $request->password])) {
            return response()->json([
                "status" => false,
                "error" => "error with password",
            ], 200);
        }

        $user = User::find(auth()->id());

        $user->forceFill([
            'password' => Hash::make($request->new_password),
            'remember_token' => Str::random(60),
        ])->save();

        return response()->json([
            "status" => true,
            'error' => null,
        ], 200);
    }
}

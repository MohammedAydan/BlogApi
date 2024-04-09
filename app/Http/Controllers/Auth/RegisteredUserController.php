<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Premission;
use App\Models\User;
use App\Models\UserPremission;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', Rules\Password::defaults()],
            'img_url' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Adjust the mime types and max size as needed
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->age = $request->age;

        // Handle image upload
        if ($request->hasFile('img_url') && $request->file('img_url')->isValid()) {
            $img_extension = $request->file('img_url')->getClientOriginalExtension();
            $path = 'Assets/Accounts';
            $fullname = time() . '.' . $img_extension;

            // Move the uploaded file to the specified path
            $request->file('img_url')->move($path, $fullname);

            // Save the image name to the user model
            $user->img_url = $fullname;
        }

        $user->password = Hash::make($request->password);
        $user->save();


        if (User::count() == 1) {
            Premission::create(["name" => "admin"]);
            Premission::create(["name" => "user"]);
            UserPremission::create(["user_id" => $user->id, "premission_id" => 1]);
            UserPremission::create(["user_id" => $user->id, "premission_id" => 2]);
        } else {
            UserPremission::create(["user_id" => $user->id, "premission_id" => 2]);
        }

        event(new Registered($user));

        Auth::login($user);

        $request->user()->tokens()->delete();

        $premissions = $request->user()->premissions->map(function ($premission) {
            return $premission->name;
        })->toArray();

        $token = $request->user()->createToken('access_token', $premissions, now()->addMonths(12));

        return response()->json([
            'user' => [
                ...$request->user()->toArray(),
                "premissions" => $premissions,
            ],
            'access_token' => $token->plainTextToken,
        ]);

        // return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {

        $request->authenticate();

        $request->session()->regenerate();

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

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}

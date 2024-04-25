<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginUserRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $accessToken = $request->user()->createToken('auth_token')->plainTextToken;
            return ['token' => $accessToken];
        }
        return response()->json(['error' => 'Wrong E-Mail or Password'], 401);
    }

    public function checkAuth(Request $request)
    {
        return UserResource::make($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out'], 200);
    }
}

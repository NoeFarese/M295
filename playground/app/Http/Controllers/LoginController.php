<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function authenticate(LoginUserRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $accessToken = $request->user()->createToken('auth_token')->plainTextToken;
            return ['token' => $accessToken];
        }
        return response()->json(['error' => 'Wrong E-Mail or Password'], 401);

    }

    public function secret()
    {
        return 'top secret (only accessible with bearer token)';
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function showMyAccount()
    {
        $user = Auth::user();
        return response()->json([
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at
            ]
        ]);
    }

    public function destroyMyAccount()
    {
        $user = Auth::user();
        $user->delete();
        return response()->json(['message' => 'Benutzer erfolgreich gelöscht.']);
    }
}

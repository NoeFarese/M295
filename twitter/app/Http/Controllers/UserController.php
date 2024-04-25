<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\TweetResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(User $user)
    {
        return UserResource::make($user);
    }

    public function tweets(User $user)
    {
        $tweets = $user->tweets()->with('user')->latest()->take(10)->get();
        return TweetResource::collection($tweets);
    }


    public function me(Request $request)
    {
        $currentUser = $request->user();
        return UserResource::make($currentUser);
    }

    public function updateMe(UpdateUserRequest $request)
    {
        $user = $request->user();
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->save();
        return UserResource::make($user);
    }

    public function deleteMe(Request $request)
    {
        $user = $request->user();
        $user->delete();
        return response()->json(['message' => 'User deleted successfully.'], 200);
    }

    public function topUsers()
    {
        $topUsers = User::withCount('tweets')
            ->orderByDesc('tweets_count')
            ->limit(3)
            ->get();

        $data = UserResource::collection($topUsers);
        return response()->json(['data' => $data]);
    }

    public function newUsers()
    {
        $newestUsers = User::orderByDesc('created_at')
            ->limit(3)
            ->get();

        $data = $newestUsers->map(function ($user) {
            return [
                'name' => $user->name,
                'created_at' => $user->created_at->toIso8601String(), // Das Datum im ISO 8601 Format zurückgeben
            ];
        });

        return response()->json(['data' => $data]);
    }
}

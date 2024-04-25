<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTweetRequest;
use App\Http\Resources\TweetResource;
use App\Models\Tweet;
use Illuminate\Http\Request;

class TweetController extends Controller
{

    public function index()
    {
        $tweets = Tweet::with('user')->take(100)->latest()->get(); //mit with('user') n+1 Problem gelöst
        return TweetResource::collection($tweets);
    }

    public function store(StoreTweetRequest $request)
    {
        $tweet = new Tweet();
        $tweet->text = $request->text;
        $tweet->user_id = $request->user()->id;
        $tweet->save();
        $tweet->load('user');
        return TweetResource::make($tweet);
    }

    public function like(Tweet $tweet)
    {
        if ($tweet->user_id === auth()->id()) {
            return response()->json(['error' => 'You cannot like your own tweet'], 403);
        }

        $tweet->likes += 1;
        $tweet->save();
        $tweet->load('user');
        return TweetResource::make($tweet);
    }
}

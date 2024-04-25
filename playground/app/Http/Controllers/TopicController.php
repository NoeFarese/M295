<?php

namespace App\Http\Controllers;


use App\Models\Post;
use App\Models\Topic;

class TopicController extends Controller
{
    public function posts(string $slug)
    {
        $topic = Topic::where('slug', $slug)->first();
        $posts = Post::where('topic_id', $topic->id)->with('topic')->get();
        return $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'topic' => $post->topic->name
            ];
        });

        /*
        return Post::with('topic')->get()->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'topic' => $post->topic,
            ];
            });
        */
    }
}

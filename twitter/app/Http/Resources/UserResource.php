<?php

namespace App\Http\Resources;

use App\Models\Tweet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at->toIso8601String(),
            'is_verified' => $this->is_verified()
        ];
    }

    public function is_verified(): bool
    {
        $totalLikes = 0;
        $tweets = Tweet::where('user_id', '=', $this->id)->get();
        foreach ($tweets as $tweet) {
            $totalLikes += $tweet->likes;
        }

        if ($totalLikes >= 100000) {
            return true;
        }
        return false;
    }
}

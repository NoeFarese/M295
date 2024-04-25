<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,
            'amount' => $this->type === 'expense' ? $this->amount * -1 : $this->amount * 1,
            'name' => $this->name,
            'type' => $this->type,
            'created_at' => $this->created_at->toIso8601String(),
            'comment' => $this->comment,
            'category' => CategoryResource::make($this->category)
        ];
    }
}

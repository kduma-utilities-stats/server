<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->prefixed_id,
            'name' => $this->name,
            'user_id' => $this->user->prefixed_id,
            'counters_count' => $this->whenCounted('counters', $this->counters_count),
        ];
    }
}

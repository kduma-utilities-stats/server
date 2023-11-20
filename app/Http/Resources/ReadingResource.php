<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReadingResource extends JsonResource
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
            'user_id' => $this->user->prefixed_id,
            'performed_on' => $this->performed_on,
            'notes' => $this->notes,
            'values_count' => $this->whenCounted('values', $this->values_count),
        ];
    }
}

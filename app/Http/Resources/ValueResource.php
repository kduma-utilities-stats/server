<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ValueResource extends JsonResource
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
            'reading_id' => $this->reading->prefixed_id,
            'counter_id' => $this->counter->prefixed_id,
            'value' => $this->value,
            'notes' => $this->notes,
        ];
    }
}

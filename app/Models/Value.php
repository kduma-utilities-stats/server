<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;

class Value extends Model
{
    use HasFactory, HasPrefixedId;

    protected $fillable = ['counter_id', 'value', 'notes'];

    public function getRouteKeyName(): string
    {
        return 'prefixed_id';
    }

    public function reading(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Reading::class);
    }

    public function counter(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Counter::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Value $value) {
            if(!$value->notes) {
                $value->notes = null;
            }
        });
    }
}

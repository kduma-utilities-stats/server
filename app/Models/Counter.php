<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;

class Counter extends Model
{
    use HasFactory, HasPrefixedId;

    protected $fillable = ['name', 'barcode'];

    public function getRouteKeyName(): string
    {
        return 'prefixed_id';
    }

    public function meter(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Meter::class);
    }

    public function values(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Value::class);
    }
}

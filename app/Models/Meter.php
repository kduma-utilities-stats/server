<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;

class Meter extends Model
{
    use HasFactory, HasPrefixedId;

    protected $fillable = ['name'];

    public function getRouteKeyName(): string
    {
        return 'prefixed_id';
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function counters(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Counter::class);
    }
}

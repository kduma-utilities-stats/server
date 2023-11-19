<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;

class Reading extends Model
{
    use HasFactory, HasPrefixedId;

    protected $fillable = ['performed_on', 'notes'];
    protected $casts = [
        'performed_on' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'prefixed_id';
    }

    protected static function booted(): void
    {
        static::creating(function (Reading $reading) {
            if($reading->performed_on === null) {
                $reading->performed_on = now();
            }

            if(!$reading->notes) {
                $reading->notes = null;
            }
        });
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function values(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Value::class);
    }
}

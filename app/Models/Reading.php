<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{
    use HasFactory;

    protected $fillable = ['performed_on'];
    protected $casts = [
        'performed_on' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Reading $reading) {
            if($reading->performed_on === null) {
                $reading->performed_on = now();
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

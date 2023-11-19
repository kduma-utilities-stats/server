<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Spatie\PrefixedIds\PrefixedIds;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PrefixedIds::registerModels([
            'c_' => \App\Models\Counter::class,
            'm_' => \App\Models\Meter::class,
            'r_' => \App\Models\Reading::class,
            'u_' => \App\Models\User::class,
            'v_' => \App\Models\Value::class,
        ]);

        PrefixedIds::generateUniqueIdUsing(function(){
            return strtolower(Str::ulid());
        });
    }
}

<?php

use App\Models\Value;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    static array $tables = ['counters', 'meters', 'readings', 'users', 'values'];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (self::$tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->string('prefixed_id')->nullable()->unique()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::$tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('prefixed_id');
            });
        }
    }
};

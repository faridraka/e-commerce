<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('postal_code');
        });

        // Pastikan tepat satu alamat default per user (yang pertama dibuat).
        // Ditulis portabel (MySQL & SQLite untuk test).
        DB::table('addresses')->update(['is_default' => false]);

        $firstIds = DB::table('addresses')
            ->selectRaw('MIN(id) as min_id')
            ->groupBy('user_id')
            ->pluck('min_id');

        DB::table('addresses')->whereIn('id', $firstIds)->update(['is_default' => true]);
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};

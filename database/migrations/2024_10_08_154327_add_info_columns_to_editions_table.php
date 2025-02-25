<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('editions', function (Blueprint $table) {
            $table->string('cover')->nullable()->after('dates');
            $table->string('venue_name')->nullable()->after('cover');
            $table->string('venue_address')->nullable()->after('venue_name');
            $table->json('links')->nullable()->after('venue_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('editions', function (Blueprint $table) {
            $table->dropColumn('cover');
            $table->dropColumn('venue_name');
            $table->dropColumn('venue_address');
            $table->dropColumn('links');
        });
    }
};

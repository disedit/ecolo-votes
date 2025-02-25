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
        Schema::table('vote_ballots', function (Blueprint $table) {
            $table->datetime('voted_at')->nullable()->after('votes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vote_ballots', function (Blueprint $table) {
            $table->dropColumn('voted_at');
        });
    }
};

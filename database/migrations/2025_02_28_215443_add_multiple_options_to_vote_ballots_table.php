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
            $table->dropForeign(['vote_option_id']);
            $table->dropColumn('vote_option_id');
            $table->json('vote_option_ids')->nullable()->after('vote_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vote_ballots', function (Blueprint $table) {
            $table->foreignId('vote_option_id')->nullable()->after('vote_id')->constrained();
            $table->dropColumn('vote_option_ids');
        });
    }
};

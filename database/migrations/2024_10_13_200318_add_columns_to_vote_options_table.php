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
        Schema::table('vote_options', function (Blueprint $table) {
            $table->boolean('is_abstain')->default(0)->after('gender');
            $table->dropColumn('disabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vote_options', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->boolean('disabled')->default(0);
        });
    }
};

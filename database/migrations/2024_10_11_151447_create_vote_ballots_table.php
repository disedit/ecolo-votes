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
        Schema::create('vote_ballots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('code_id')->constrained();
            $table->foreignId('vote_id')->constrained();
            $table->foreignId('vote_option_id')->nullable()->constrained();
            $table->integer('votes')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vote_ballots');
    }
};

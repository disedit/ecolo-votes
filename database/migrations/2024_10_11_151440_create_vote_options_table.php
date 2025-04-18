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
        Schema::create('vote_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vote_id')->constrained();
            $table->string('name');
            $table->string('description')->nullable();
            $table->enum('gender', ['F', 'M', 'O'])->nullable();
            $table->foreignId('region_id')->nullable()->constrained();
            $table->boolean('disabled')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vote_options');
    }
};

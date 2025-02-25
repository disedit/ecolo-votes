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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edition_id')->constrained();
            $table->string('name');
            $table->string('subtitle')->nullable();
            $table->enum('majority', ['simple', 'absolute', '2/3_all', '3/4_all', '2/3_cast', '3/4_cast'])->default('simple');
            $table->boolean('open')->default(0);
            $table->datetime('opened_at')->nullable();
            $table->datetime('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};

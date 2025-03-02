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
            $table->enum('type', ['yesno', 'options'])->default('yesno');
            $table->enum('majority', ['simple', '50', '2/3'])->default('simple');
            $table->boolean('with_abstentions')->default(0);
            $table->enum('relative_to', ['turnout', 'votes_cast'])->default('turnout');
            $table->integer('max_votes')->default(1);
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

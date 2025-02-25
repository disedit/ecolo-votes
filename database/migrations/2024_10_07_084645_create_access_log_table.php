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
        Schema::create('access_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendee_id')->constrained();
            $table->enum('type', ['IN', 'OUT']);
            $table->string('client');
            $table->foreignId('logged_by_user_id')->nullable()->constrained(table: 'users', indexName: 'logged_by_user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_log');
    }
};

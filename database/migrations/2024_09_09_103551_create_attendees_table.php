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
        Schema::create('attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edition_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('type_id')->constrained();
            $table->string('qr_code')->unique();
            $table->datetime('checked_in')->nullable();
            $table->boolean('notified')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendants');
    }
};

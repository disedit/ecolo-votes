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
        Schema::create('attendee_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendee_id')->constrained();
            $table->string('field_key');
            $table->string('field_label')->nullable();
            $table->text('field_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendee_details');
    }
};

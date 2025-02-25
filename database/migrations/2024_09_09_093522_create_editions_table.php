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
        Schema::create('editions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('short_title', 10);
            $table->date('date_start');
            $table->date('date_end');
            $table->boolean('current')->default(0);
            $table->string('form_id')->nullable();
            $table->string('press_form_id')->nullable();
            $table->json('form_mappings')->nullable();
            $table->string('location')->nullable();
            $table->string('dates')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editions');
    }
};

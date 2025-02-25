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
            $table->foreignId('fee_id')->nullable()->constrained();
            $table->string('qr_code');
            $table->datetime('checked_in')->nullable();
            $table->foreignId('registered_by_user_id')->nullable()->constrained(table: 'users', indexName: 'registered_by_user_id');
            $table->bigInteger('form_submission_id')->nullable();
            $table->boolean('confirmed')->default(0);
            $table->boolean('paid')->default(0);
            $table->boolean('notifiable')->default(0);
            $table->boolean('notified')->default(0);
            $table->boolean('ticket_notified')->default(0);
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

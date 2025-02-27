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
        Schema::table('attendees', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->after('edition_id')->constrained();
            $table->string('first_name')->after('group_id');
            $table->string('last_name')->after('first_name');
            $table->string('email')->after('last_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('token')->after('qr_code');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendees', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('email');
            $table->dropColumn('phone');
            $table->foreignId('user_id')->after('edition_id')->constrained();
        });
    }
};

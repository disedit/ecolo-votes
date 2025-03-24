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
        Schema::table('editions', function (Blueprint $table) {
            $table->text('mail_notification_subject')->nullable()->after('links');
            $table->text('mail_notification_body')->nullable()->after('mail_notification_subject');
            $table->text('sms_notification')->nullable()->after('mail_notification_body');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('editions', function (Blueprint $table) {
            $table->dropColumn('mail_notification_subject');
            $table->dropColumn('mail_notification_body');
            $table->dropColumn('sms_notification');
        });
    }
};

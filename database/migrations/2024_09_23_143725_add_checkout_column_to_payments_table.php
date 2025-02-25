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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('checkout_session_id')->nullable()->after('status');
            $table->text('checkout_client_secret')->nullable()->after('checkout_session_id');
            $table->boolean('completed_checkout')->default(0)->after('checkout_client_secret');
            $table->boolean('error_notified')->default(0)->after('completed_checkout');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('checkout_session_id');
            $table->dropColumn('checkout_client_secret');
            $table->dropColumn('completed_checkout');
            $table->dropColumn('error_notified');
        });
    }
};

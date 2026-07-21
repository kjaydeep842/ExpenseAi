<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('gpay_connected')->default(false)->after('phone');
            $table->string('gpay_oauth_token')->nullable()->after('gpay_connected');
            $table->timestamp('gpay_linked_at')->nullable()->after('gpay_oauth_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gpay_connected', 'gpay_oauth_token', 'gpay_linked_at']);
        });
    }
};

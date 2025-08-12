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
        Schema::table('message_private', function (Blueprint $table) {
            $table->string('voice_url')->nullable()->after('content');
            $table->string('file_url')->nullable()->after('voice_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message_private', function (Blueprint $table) {
            $table->dropColumn(['voice_url','file_url']);
        });
    }
};

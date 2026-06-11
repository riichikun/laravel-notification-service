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
        Schema::create('recipient_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('notification_request_id');
            $table->uuid('recipient_id'); // ID подписчика
            $table->string('status')->default('queued');
            $table->text('error_message')->nullable();
            $table->string('channel');
            $table->text('message_text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipient_messages');
    }
};

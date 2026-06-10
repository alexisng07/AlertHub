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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('subscriber_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('alert_rule_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('channel', [
                'email',
                'webhook',
            ]);

            $table->string('subject');
            $table->longText('body');

            $table->json('payload')->nullable();

            $table->enum('status', [
                'pending',
                'sent',
                'failed',
                'escalated',
            ])->default('pending');

            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

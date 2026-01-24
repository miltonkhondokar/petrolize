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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id'); // Primary key
            $table->uuid('uuid')->unique();

            // User who triggered the action
            $table->unsignedBigInteger('user_id')->nullable()->index();

            // Human-readable description of what happened
            $table->text('action');

            // Logical type/category (like your old 'type' column)
            $table->string('type', 100)->default('others')->index();

            // Optional event type (create, update, delete, receive, login, error, etc.)
            $table->string('event', 50)->nullable()->index();

            // Optional related model info
            $table->string('model', 100)->nullable();         // e.g., FuelPurchase
            $table->uuid('model_uuid')->nullable()->index();  // UUID of the model instance
            $table->unsignedBigInteger('item_id')->nullable()->index(); // Old compatibility

            // Optional exception info
            $table->string('exception_class', 255)->nullable();
            $table->string('exception_code', 50)->nullable();

            // Request context
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            // Optional extra JSON meta for future-proofing
            $table->json('meta')->nullable();

            $table->timestamps(); // created_at + updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

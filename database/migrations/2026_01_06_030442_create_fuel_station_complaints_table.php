<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fuel_station_complaints', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();
            $table->uuid('fuel_station_uuid')->index();
            $table->string('category')->nullable(); // e.g., fuel_shortage, nozzle_issue, power_failure
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->index()->default('open'); // open, in_progress, resolved
            $table->date('complaint_date');
            $table->date('resolved_date')->nullable();
            $table->boolean('is_active')->index()->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_station_complaints');
    }
};

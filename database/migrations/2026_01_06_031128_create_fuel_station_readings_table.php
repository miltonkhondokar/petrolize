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
        Schema::create('fuel_station_readings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();
            $table->uuid('fuel_station_uuid')->index();
            $table->uuid('fuel_type_uuid')->index();
            $table->integer('nozzle_number')->index();  // 1,2,3...
            $table->decimal('reading', 12, 3); // meter reading in liters
            $table->date('reading_date')->index();
            $table->boolean('is_active')->index()->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_station_readings');
    }
};

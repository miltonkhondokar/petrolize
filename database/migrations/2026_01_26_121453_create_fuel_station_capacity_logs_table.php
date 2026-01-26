<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('fuel_station_capacity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();

            $table->uuid('fuel_station_uuid')->index();
            $table->uuid('fuel_type_uuid')->index();

            // capacity at that point in time
            $table->decimal('capacity_liters', 14, 3);

            // when this capacity became effective
            $table->dateTime('effective_from')->index();

            $table->text('note')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(
                ['fuel_station_uuid', 'fuel_type_uuid', 'effective_from'],
                'idx_station_fuel_time'
            );

            $table->unique(
                ['fuel_station_uuid', 'fuel_type_uuid', 'effective_from'],
                'uniq_station_fuel_time'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_station_capacity_logs');
    }
};

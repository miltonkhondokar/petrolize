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
        Schema::create('fuel_stations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();
            $table->uuid('user_uuid')->nullable()->index()->comment('Manager of the fuel station');
            $table->string('name');
            $table->uuid('region_uuid')->nullable()->index()->comment('Region where the fuel station is located');
            $table->uuid('governorate_uuid')->nullable()->index()->comment('Governorate of the fuel station');
            $table->uuid('center_uuid')->nullable()->index()->comment('Center/Markaz of the fuel station');
            $table->uuid('city_uuid')->nullable()->index()->comment('City/Village of the fuel station');
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_stations');
    }
};

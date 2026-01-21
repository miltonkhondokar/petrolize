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

        Schema::create('fuel_purchase_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();

            $table->uuid('fuel_purchase_uuid')->index();
            $table->uuid('fuel_type_uuid')->index();
            $table->uuid('fuel_unit_uuid')->index();

            $table->decimal('quantity', 12, 3);
            $table->decimal('received_qty', 12, 3)->default(0);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('line_total', 14, 2)->default(0);

            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->unique(['fuel_purchase_uuid', 'fuel_type_uuid'], 'uniq_purchase_fueltype');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_purchase_items');
    }
};
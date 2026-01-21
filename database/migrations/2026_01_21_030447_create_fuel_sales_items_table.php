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

        Schema::create('fuel_sales_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();

            $table->uuid('fuel_sales_day_uuid')->index();
            $table->uuid('fuel_type_uuid')->index();

            $table->integer('nozzle_number')->nullable()->index();

            $table->decimal('opening_reading', 12, 3)->default(0);
            $table->decimal('closing_reading', 12, 3)->default(0);
            $table->decimal('sold_qty', 12, 3)->default(0);

            $table->decimal('price_per_unit', 10, 2)->default(0);
            $table->decimal('line_total', 14, 2)->default(0);

            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['fuel_sales_day_uuid', 'fuel_type_uuid']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_sales_items');
    }
};
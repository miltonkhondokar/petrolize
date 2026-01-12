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
        Schema::create('pump_fuel_stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();
            $table->uuid('pump_uuid')->index();
            $table->uuid('vendor_uuid')->index()->nullable()->comment('Vendor supplying the fuel stock');
            $table->uuid('fuel_type_uuid')->index();
            $table->decimal('quantity', 12, 3);
            $table->uuid('fuel_unit_uuid')->index()->nullable()->comment('Fuel unit reference');
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('total_cost', 14, 2)->nullable();
            $table->string('reference_no')->nullable();
            $table->date('stock_date')->index();
            $table->boolean('is_initial_stock')->index()->default(false);
            $table->boolean('is_active')->index()->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pump_fuel_stocks');
    }
};

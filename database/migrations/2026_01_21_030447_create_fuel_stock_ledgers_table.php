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

        Schema::create('fuel_stock_ledgers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();

            $table->uuid('fuel_station_uuid')->index();
            $table->uuid('fuel_type_uuid')->index();
            $table->uuid('fuel_unit_uuid')->index();

            $table->string('txn_type')->index(); // purchase_receive|sale|adjustment|transfer_in|transfer_out
            $table->uuid('ref_uuid')->nullable()->index(); // purchase uuid or sales_day uuid
            $table->date('txn_date')->index();

            $table->decimal('qty_in', 12, 3)->default(0);
            $table->decimal('qty_out', 12, 3)->default(0);
            $table->decimal('balance_after', 12, 3)->nullable();

            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['fuel_station_uuid', 'fuel_type_uuid', 'txn_date'], 'idx_station_fuel_date');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_stock_ledgers');
    }
};
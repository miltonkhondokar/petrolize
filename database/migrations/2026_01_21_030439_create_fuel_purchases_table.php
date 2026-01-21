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

        Schema::create('fuel_purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();

            $table->uuid('fuel_station_uuid')->index();  // required
            $table->uuid('vendor_uuid')->index();

            $table->date('purchase_date')->index();
            $table->string('invoice_no')->nullable()->index();

            $table->string('transport_by')->default('vendor')->index(); // vendor|owner
            $table->string('truck_no')->nullable();

            $table->string('status')->default('draft')->index(); // draft|received_partial|received_full|cancelled
            $table->decimal('total_amount', 14, 2)->default(0);

            $table->text('note')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_purchases');
    }
};
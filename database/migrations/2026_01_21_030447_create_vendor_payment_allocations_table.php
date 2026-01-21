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

        Schema::create('vendor_payment_allocations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();

            $table->uuid('vendor_payment_uuid')->index();
            $table->uuid('fuel_purchase_uuid')->index();

            $table->decimal('allocated_amount', 14, 2);
            $table->timestamps();

            $table->unique(['vendor_payment_uuid', 'fuel_purchase_uuid'], 'uniq_payment_purchase');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_payment_allocations');
    }
};
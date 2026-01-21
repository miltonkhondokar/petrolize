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

        Schema::create('fuel_sales_days', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();

            $table->uuid('fuel_station_uuid')->index();
            $table->uuid('user_uuid')->nullable()->index(); // manager

            $table->date('sale_date')->index();
            $table->string('status')->default('draft')->index(); // draft|submitted|approved

            $table->decimal('cash_amount', 14, 2)->default(0);
            $table->decimal('bank_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);

            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['fuel_station_uuid', 'sale_date'], 'uniq_station_saledate');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_sales_days');
    }
};
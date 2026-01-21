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

        Schema::create('vendor_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();

            $table->uuid('vendor_uuid')->index();
            $table->uuid('created_by_user_uuid')->nullable()->index();

            $table->date('payment_date')->index();
            $table->string('method')->default('cash')->index(); // cash|bank
            $table->decimal('amount', 14, 2);

            $table->string('reference_no')->nullable()->index();
            $table->text('note')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_payments');
    }
};
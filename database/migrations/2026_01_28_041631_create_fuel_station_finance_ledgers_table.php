<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('fuel_station_finance_ledgers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();

            // Station this ledger belongs to
            $table->uuid('fuel_station_uuid')->index();

            /**
             * txn_type values (STRICT):
             * fuel_issue       -> owner gives fuel value (DEBIT)
             * owner_expense    -> owner paid expense (DEBIT)
             * cash_received    -> station returned cash (CREDIT)
             * adjustment       -> manual correction
             */
            $table->string('txn_type')->index();

            // Accounting date
            $table->date('txn_date')->index();

            // Money
            $table->decimal('debit_amount', 14, 2)->default(0);
            $table->decimal('credit_amount', 14, 2)->default(0);

            // Reference for drill-down (sales_day, payment, cost_entry etc.)
            $table->string('ref_table')->nullable();
            $table->uuid('ref_uuid')->nullable()->index();

            $table->text('note')->nullable();

            // Audit
            $table->uuid('created_by')->nullable()->index();
            $table->timestamps();

            $table->index(['fuel_station_uuid', 'txn_date'], 'idx_station_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_station_finance_ledgers');
    }
};

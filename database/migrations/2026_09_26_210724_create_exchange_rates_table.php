<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('base_currency_id')->constrained('currencies');
            $table->foreignId('target_currency_id')->constrained('currencies');
            $table->decimal('rate', 20, 10);
            $table->dateTimeTz('valid_on')->useCurrent();
            $table->timestamps();

            $table->unique(
                columns: ['base_currency_id', 'target_currency_id', 'valid_on'],
                name: 'base_to_target_exchange_rates_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};

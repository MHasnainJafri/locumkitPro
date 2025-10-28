<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('finance_balancesheets', function (Blueprint $table) {
            $table->float('profit_plan_equip')->nullable();
            $table->float('trade_other')->nullable();
            $table->float('cash_equp')->nullable();
            $table->float('total_cash_trade')->nullable();
            $table->float('total_assets')->nullable();
            $table->float('current_liability')->nullable();
            $table->float('taxation')->nullable();
            $table->float('total_tax_liab')->nullable();
            $table->float('net_assests_liab')->nullable();
            $table->float('equity')->nullable();
            $table->float('retained_earning')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finance_balancesheets', function (Blueprint $table) {
            //
        });
    }
};

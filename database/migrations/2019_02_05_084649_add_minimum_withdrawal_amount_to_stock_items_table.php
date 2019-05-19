<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMinimumWithdrawalAmountToStockItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_items', function (Blueprint $table) {
            $table->decimal('minimum_withdrawal_amount', 19,8)->default(0)->unsigned()->after('withdrawal_fee');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_items', function (Blueprint $table) {
            $table->dropColumn(['minimum_withdrawal_amount']);
        });
    }
}

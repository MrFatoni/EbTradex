<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReferralEarningToStockExchangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_exchanges', function (Blueprint $table) {
            $table->decimal('referral_earning',19,8)->default(0)->after('fee');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_exchanges', function (Blueprint $table) {
            $table->dropColumn('referral_earning');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferralEarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_earnings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('referrer_user_id')->unsigned();
            $table->integer('referral_user_id')->unsigned();
            $table->integer('stock_item_id')->unsigned();
            $table->decimal('amount',19,8)->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_earnings');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('stock_pair_id')->unsigned();
            $table->integer('category')->comment('1 => exchange, 2 => margin, 3 => lending, 4 => ico');
            $table->integer('exchange_type')->comment('1 => buy, 2 => sell');
            $table->decimal('price', 19, 8)->unsigned();
            $table->decimal('amount', 19, 8)->unsigned();
            $table->decimal('exchanged', 19, 8)->unsigned()->default(0);
            $table->decimal('canceled', 19, 8)->unsigned()->default(0);
            $table->decimal('stop_limit', 19, 8)->unsigned()->nullable();
            $table->decimal('maker_fee', 19, 8)->unsigned()->default(0);
            $table->decimal('taker_fee', 19, 8)->unsigned()->default(0);
            $table->integer('status')->default(STOCK_ORDER_PENDING);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('stock_pair_id')->references('id')->on('stock_pairs')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_orders');
    }
}

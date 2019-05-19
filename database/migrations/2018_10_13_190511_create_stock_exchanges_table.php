<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockExchangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_exchanges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('stock_exchange_group_id')->nullable()->unsigned();
            $table->integer('stock_order_id')->unsigned();
            $table->integer('stock_pair_id')->unsigned();
            $table->decimal('amount', 19, 8)->unsigned();
            $table->decimal('price', 19, 8)->unsigned();
            $table->decimal('total', 19, 8)->unsigned();
            $table->decimal('fee', 19, 8)->unsigned();
            $table->integer('exchange_type');
            $table->integer('related_order_id')->nullable()->comment('Must reserved the opposite transaction id.');
            $table->integer('is_maker');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('stock_exchange_group_id')->references('id')->on('stock_exchange_groups')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('stock_pair_id')->references('id')->on('stock_pairs')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('stock_order_id')->references('id')->on('stock_orders')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_exchanges');
    }
}

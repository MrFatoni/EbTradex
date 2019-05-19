<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockGraphDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_graph_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('stock_pair_id')->unsigned();
            $table->text('5min');
            $table->text('15min');
            $table->text('30min');
            $table->text('2hr');
            $table->text('4hr');
            $table->text('1day');
            $table->timestamps();

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
        Schema::dropIfExists('stock_graph_datas');
    }
}

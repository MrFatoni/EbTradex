<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('stock_item_id')->unsigned();
            $table->string('model_name')->nullable();
            $table->integer('model_id')->unsigned()->nullable();
            $table->integer('transaction_type')->comment('1 => debit, 2 => credit');
            $table->decimal('amount', 19, 8);
            $table->integer('journal')->comment('From user to user transaction record');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('stock_item_id')->references('id')->on('stock_items')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}

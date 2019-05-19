<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('ref_id');
            $table->integer('user_id')->unsigned();
            $table->integer('wallet_id')->unsigned();
            $table->integer('stock_item_id')->unsigned();
            $table->decimal('amount', 19, 8)->unsigned();
            $table->decimal('network_fee', 19, 8)->default(0)->unsigned();
            $table->decimal('system_fee', 19, 8)->default(0)->unsigned();
            $table->string('address')->nullable();
            $table->string('txn_id')->nullable();
            $table->integer('payment_method')->nullable();
            $table->integer('status')->default(PAYMENT_PENDING);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('stock_item_id')->references('id')->on('stock_items')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deposits');
    }
}

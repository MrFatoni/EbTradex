<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('stock_item_id')->unsigned();
            $table->decimal('primary_balance', 19, 8)->unsigned()->default(0);
            $table->decimal('on_order_balance', 19, 8)->unsigned()->default(0);
            $table->string('address')->nullable();
            $table->integer('is_active')->default(ACTIVE_STATUS_ACTIVE);
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
        Schema::dropIfExists('wallets');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('item')->index();
            $table->string('item_name')->index();
            $table->integer('item_type');
            $table->string('item_emoji')->nullable();
            $table->integer('is_active')->default(ACTIVE_STATUS_ACTIVE);
            $table->integer('exchange_status')->default(ACTIVE_STATUS_ACTIVE);
            $table->integer('is_fee_applicable')->default(ACTIVE_STATUS_ACTIVE);
            $table->integer('is_ico')->default(ACTIVE_STATUS_INACTIVE);
            $table->integer('deposit_status')->default(ACTIVE_STATUS_INACTIVE);
            $table->decimal('deposit_fee', 13,2)->unsigned()->default(0);
            $table->integer('withdrawal_status')->default(ACTIVE_STATUS_INACTIVE);
            $table->decimal('withdrawal_fee', 13,2)->unsigned()->default(0);
            $table->decimal('daily_withdrawal_limit', 19,8)->unsigned()->default(25000);
            $table->integer('api_service')->nullable();
            $table->decimal('total_withdrawal', 19, 8)->unsigned()->default(0);
            $table->decimal('total_withdrawal_fee', 19, 8)->unsigned()->default(0);
            $table->decimal('total_deposit', 19, 8)->unsigned()->default(0);
            $table->decimal('total_deposit_fee', 19, 8)->unsigned()->default(0);
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
        Schema::dropIfExists('stock_items');
    }
}

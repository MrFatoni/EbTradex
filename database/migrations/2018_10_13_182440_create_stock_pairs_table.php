<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockPairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_pairs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('stock_item_id')->unsigned();
            $table->integer('base_item_id')->unsigned();
            $table->integer('is_active')->default(ACTIVE_STATUS_ACTIVE);
            $table->integer('is_default')->default(ACTIVE_STATUS_INACTIVE);
            // 24 hour related
            $table->longText('exchange_24')->nullable();
            $table->decimal('last_price', 19, 8)->unsigned()->default(0);
            // life time
            # Order table Related (+ and -)
            $table->decimal('base_item_buy_order_volume', 19, 8)->unsigned()->default(0);
            $table->decimal('stock_item_buy_order_volume', 19, 8)->unsigned()->default(0);
            $table->decimal('base_item_sale_order_volume', 19, 8)->unsigned()->default(0);
            $table->decimal('stock_item_sale_order_volume', 19, 8)->unsigned()->default(0);
            # Exchange table related (only +)
            $table->decimal('exchanged_buy_total', 19, 8)->unsigned()->default(0);
            $table->decimal('exchanged_sale_total', 19, 8)->unsigned()->default(0);
            #fees
            $table->decimal('exchanged_amount', 19, 8)->unsigned()->default(0);
            $table->decimal('exchanged_maker_total', 19, 8)->unsigned()->default(0);

            $table->decimal('exchanged_buy_fee', 19, 8)->unsigned()->default(0);
            $table->decimal('exchanged_sale_fee', 19, 8)->unsigned()->default(0);

            $table->decimal('ico_total_sold', 19, 8)->unsigned()->default(0);
            $table->decimal('ico_total_earned', 19, 8)->unsigned()->default(0)->comment('ico total earned with fee');
            $table->decimal('ico_fee_earned', 19, 8)->unsigned()->default(0);
            $table->timestamps();

            $table->unique(['stock_item_id', 'base_item_id']);
            $table->foreign('stock_item_id')->references('id')->on('stock_items')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('base_item_id')->references('id')->on('stock_items')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_pairs');
    }
}

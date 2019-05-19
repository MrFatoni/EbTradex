<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRoleManagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_role_managements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('role_name');
            $table->text('route_group')->nullable();
            $table->boolean('is_active')->default(ACTIVE_STATUS_ACTIVE);
//            $table->softDeletes();
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
        Schema::drop('user_role_managements');
    }
}

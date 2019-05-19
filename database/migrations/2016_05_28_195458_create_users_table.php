<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_role_management_id')->unsigned();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('is_email_verified')->default(EMAIL_VERIFICATION_STATUS_INACTIVE);
            $table->string('is_financial_active')->default(FINANCIAL_STATUS_ACTIVE);
            $table->string('is_accessible_under_maintenance')->default(UNDER_MAINTENANCE_ACCESS_INACTIVE);
            $table->string('avatar')->nullable();
            $table->string('google2fa_secret')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('created_by_admin')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_role_management_id')->references('id')->on('user_role_managements')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}

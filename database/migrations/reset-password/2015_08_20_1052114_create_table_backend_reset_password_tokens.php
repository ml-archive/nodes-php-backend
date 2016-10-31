<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateTableBackendResetPasswordTokens.
 */
class CreateTableBackendResetPasswordTokens extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('backend_reset_password_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 255)->index();
            $table->string('token', 64)->index();
            $table->boolean('used')->unsigned()->default(false);
            $table->timestamp('expire_at');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::drop('backend_reset_password_tokens');
    }
}

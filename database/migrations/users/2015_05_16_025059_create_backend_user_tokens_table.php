<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBackendUserTokensTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('backend_user_tokens', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('backend_users')->onDelete('cascade');
            $table->string('token', 60);
            $table->dateTime('expire')->nullable();
            $table->timestamps();
        });
        
         // Update "token" to be binary, so it's case sensitive
        DB::statement('ALTER TABLE `backend_user_tokens` CHANGE `token` `token` VARCHAR(60) BINARY NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('backend_user_tokens');
    }

}

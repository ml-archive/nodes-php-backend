<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBackendUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('backend_users', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('image_path')->nullable();
            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->string('user_role', 255)->default('user')->index();
            $table->foreign('user_role')->references('slug')->on('backend_roles')->onDelete('cascade');
            $table->boolean('change_password')->unsigned()->default(false);
            $table->rememberToken();
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
        Schema::dropIfExists('backend_users');
    }

}

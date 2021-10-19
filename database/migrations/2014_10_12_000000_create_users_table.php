<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            $table->id();

            $table->integer('chat_id')->default(0);
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('username')->nullable();
            $table->string('role')->default('guest');
            $table->integer('external_id')->nullable();
            $table->string('phone_number')->nullable();

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
        Schema::dropIfExists('users');
    }
}

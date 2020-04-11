<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatabasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('databases', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->enum('driver', ['mysql', 'postgresql', 'sqlite']);
            $table->string('host', 255)->default('localhost');
            $table->smallInteger('port')->unsigned()->nullable();
            $table->string('name');
            $table->string('user', 255);
            $table->string('password', 255);
            $table->timestamps();
            $table->index('user_id');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('databases');
    }
}

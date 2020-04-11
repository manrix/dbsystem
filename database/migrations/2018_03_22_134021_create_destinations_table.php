<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDestinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->enum('driver', ['ftp', 'dropbox', 'g3', 'local']);
            $table->string('name');
            $table->string('host', 255)->nullable();
            $table->string('user', 255)->nullable();
            $table->string('password', 255)->nullable();
            $table->smallInteger('port')->unsigned()->nullable();
            $table->string('root', 255)->nullable();
            $table->text('token')->nullable();
            $table->string('client_id', 255)->nullable();
            $table->text('client_secret')->nullable();
            $table->text('refresh_token')->nullable();
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
        Schema::dropIfExists('destinations');
    }
}

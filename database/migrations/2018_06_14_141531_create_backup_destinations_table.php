<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBackupDestinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('backup_destinations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('backup_id')->unsigned();
            $table->integer('destination_id')->unsigned();
            $table->string('path', 255)->nullable();
            $table->timestamps();
            $table->index('backup_id');
            $table->index('destination_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('backup_destinations');
    }
}

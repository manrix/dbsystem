<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskDatabasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_databases', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('task_id')->unsigned();
            $table->integer('database_id')->unsigned();
            $table->text('tables')->nullable();
            $table->boolean('skip_comments');
            $table->boolean('use_extended_inserts');
            $table->boolean('use_single_transaction');
            $table->boolean('use_inserts');
            $table->boolean('use_compression')->default(0);
            $table->timestamps();
            $table->index('task_id');
            $table->index('database_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_databases');
    }
}

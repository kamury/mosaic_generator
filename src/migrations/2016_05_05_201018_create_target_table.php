<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTargetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mosaic_generator_target', function($table)
    {
      $table->integer('event_id')->unsigned();
      $table->foreign('event_id')->references('id')->on('mosaic_events')->onDelete('cascade');
      $table->string('target_url');
      $table->tinyInteger('is_parsed')->default(0);
      $table->integer('cell_height')->unsigned();
      $table->integer('cell_width')->unsigned();
      $table->integer('columns')->unsigned();
      $table->integer('rows')->unsigned();
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
		Schema::drop('mosaic_generator_target');
	}

}

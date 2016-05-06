<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParsedTargetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mosaic_generator_parsed_target', function($table)
    {
      $table->increments('id');
      $table->integer('event_id')->unsigned();
      $table->foreign('event_id')->references('id')->on('mosaic_events')->onDelete('cascade');
      $table->tinyInteger('is_filled')->default(0);
      $table->integer('x')->unsigned();
      $table->integer('y')->unsigned();
      $table->integer('red')->unsigned();
      $table->integer('green')->unsigned();
      $table->integer('blue')->unsigned();
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
		Schema::drop('mosaic_generator_parsed_target');
	}

}

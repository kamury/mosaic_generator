<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThumbnailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mosaic_generator_thumbnails', function($table)
    {
      $table->increments('id');
      $table->integer('event_id')->unsigned();
      $table->foreign('event_id')->references('id')->on('mosaic_events')->onDelete('cascade');
      $table->string('target_url');
      $table->string('processed_image_url');
      $table->string('masked_image_url');
      $table->string('original_image_url');
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
		Schema::drop('mosaic_generator_thumbnails');
	}

}

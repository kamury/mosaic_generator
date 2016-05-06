<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMosaicPrintQueueImages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mosaic_print_queue_images', function($table)
    {
      $table->increments('id');
      $table->integer('thumb_id')->unsigned();
      $table->foreign('thumb_id')->references('id')->on('mosaic_generator_thumbnails')->onUpdate('cascade')->onDelete('cascade');
      $table->enum('print_status', array('in queue', 'downloading', 'downloaded', 'printed'));
      $table->integer('event_id')->unsigned();
      $table->foreign('event_id')->references('id')->on('mosaic_events')->onDelete('cascade');
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
		Schema::drop('mosaic_print_queue_images');
	}

}

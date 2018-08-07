<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHighresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mosaic_generator_highres', function($table)
    {
      $table->integer('event_id')->unsigned();
      $table->primary('event_id');
      $table->foreign('event_id')->references('id')->on('mosaic_events')->onDelete('cascade');
      $table->string('url1')->nullable();
      $table->string('url2')->nullable();
      $table->string('url3')->nullable();
      $table->string('url4')->nullable();
      $table->string('url')->nullable();
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
		Schema::drop('mosaic_generator_highres');
	}

}

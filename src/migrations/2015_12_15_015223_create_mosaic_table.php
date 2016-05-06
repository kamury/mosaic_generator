<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMosaicTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mosaic_events', function($table)
    {
      $table->increments('id');
      $table->integer('client_id')->unsigned();
      $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade')->onUpdate('cascade');
      $table->string('name');
      $table->string('target_url');
      $table->dateTime('started')->nullable()->default(NULL);
      $table->tinyInteger('auto_approval')->default(0);
      $table->tinyInteger('is_load_from_instagram')->default(0);
      $table->tinyInteger('is_mobile_upload')->default(0);
      $table->tinyInteger('is_load_from_set')->default(0);
      $table->tinyInteger('is_printing_enabled')->default(0);
      $table->string('printer_queue_hash');
      $table->string('text_color');
      $table->integer('company_id')->unsigned()->nullable();
      $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
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
		Schema::drop('mosaic_events');
  }
}
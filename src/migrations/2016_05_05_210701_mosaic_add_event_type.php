<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MosaicAddEventType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('hashtags', function($table)
    {
      $table->enum('event_type', array('1', '2'))->default('1')->comment('1-regular event, 2-mosaic event');
    });
    
    Schema::table('instagram_images', function($table)
    {
      $table->enum('event_type', array('1', '2'))->default('1')->comment('1-regular event, 2-mosaic event');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('hashtags', function($table)
    {
      $table->dropColumn('event_type');
    });
    
    Schema::table('instagram_images', function($table)
    {
      $table->dropColumn('event_type');
    });
	}

}

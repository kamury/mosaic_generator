<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMosaicEventsTableAddMosaicSize extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('mosaic_events', function($table)
    {
      $table->enum('mosaic_size', array('64x48', '32x24', '16x12'))->default('64x48');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('mosaic_events', function($table)
    {
      $table->dropColumn('mosaic_size');
    });
	}

}

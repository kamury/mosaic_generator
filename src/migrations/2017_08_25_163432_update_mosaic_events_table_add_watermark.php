<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMosaicEventsTableAddWatermark extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('mosaic_events', function($table)
    {
      $table->tinyInteger('watermark')->default(65);
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
      $table->dropColumn('watermark');
    });
	}

}

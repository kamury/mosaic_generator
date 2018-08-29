<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMosaicEventAddHighresUrl extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('mosaic_events', function($table)
    {
      $table->string('highres_url')->nullable()->default(NULL);
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	  Schema::table('mosaic_events', function($table) {
		  $table->dropColumn('highres_url');
    });
	}

}

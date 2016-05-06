<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMosaicGeneratorTarget extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('mosaic_generator_target', function($table)
    {
      $table->primary('event_id');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	  Schema::table('mosaic_generator_target', function($table)
    {
      $table->dropPrimary('event_id');
    });
	}

}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateParsedTargetTableAddSourceImg extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('mosaic_generator_parsed_target', function($table)
    {
      $table->string('source_cell')->after('event_id');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('mosaic_generator_parsed_target', function($table)
    {
      $table->dropColumn('source_cell');
    });
	}

}

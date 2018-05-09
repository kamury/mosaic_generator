<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateGeneratorParsedTargetIndexes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	  Schema::table('mosaic_generator_parsed_target', function($table)
    {
      $table->unique(array('event_id', 'x', 'y'));
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
      $table->drop_unique(array('event_id', 'x', 'y'));
    });
		
	}

}

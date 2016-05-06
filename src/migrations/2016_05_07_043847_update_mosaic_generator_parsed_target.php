<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMosaicGeneratorParsedTarget extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	  DB::statement('ALTER TABLE `mosaic_generator_parsed_target` CHANGE `red` `red` INT( 10 ) NOT NULL');
    DB::statement('ALTER TABLE `mosaic_generator_parsed_target` CHANGE `green` `green` INT( 10 ) NOT NULL');
    DB::statement('ALTER TABLE `mosaic_generator_parsed_target` CHANGE `blue` `blue` INT( 10 ) NOT NULL');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{ 
    DB::statement('ALTER TABLE `mosaic_generator_parsed_target` CHANGE `red` `red` INT( 10 ) UNSIGNED NOT NULL');
    DB::statement('ALTER TABLE `mosaic_generator_parsed_target` CHANGE `green` `green` INT( 10 ) UNSIGNED NOT NULL');
    DB::statement('ALTER TABLE `mosaic_generator_parsed_target` CHANGE `blue` `blue` INT( 10 ) UNSIGNED NOT NULL');
	}

}

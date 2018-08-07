<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTargetAddPrintSizes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('mosaic_generator_target', function($table)
    {
      $table->integer('print_width')->default(300);
      $table->integer('print_height')->default(450);
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
      $table->dropColumn('print_width');
      $table->dropColumn('print_height');
    });
	}

}

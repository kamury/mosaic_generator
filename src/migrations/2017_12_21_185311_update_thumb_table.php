<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateThumbTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('mosaic_generator_thumbnails', function($table)
    {
      $table->enum('source_type', array('instagram', 'mobile upload', 'bulk upload'))->after('event_id')->default('instagram');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('mosaic_generator_thumbnails', function($table)
    {
      $table->dropColumn('source_type');
    });
	}

}

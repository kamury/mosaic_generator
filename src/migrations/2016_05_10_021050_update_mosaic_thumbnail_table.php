<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMosaicThumbnailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('mosaic_generator_thumbnails', function($table)
    {
      $table->string('thumb_url');
      $table->string('current_mosaic_url');
      $table->datetime('expired_at');
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
      $table->dropColumn('thumb_url');
      $table->dropColumn('current_mosaic_url');
      $table->dropColumn('expired_at');
    });
	}

}

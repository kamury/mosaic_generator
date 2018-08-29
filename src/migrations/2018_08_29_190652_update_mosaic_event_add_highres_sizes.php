<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMosaicEventAddHighresSizes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('mosaic_events', function($table) {
		  $table->string('highres_A0')->nullable()->default(NULL);
      $table->string('highres_A1')->nullable()->default(NULL);
      $table->string('highres_A2')->nullable()->default(NULL);
      $table->string('highres_A3')->nullable()->default(NULL);
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
		  $table->dropColumn('highres_A0');
      $table->dropColumn('highres_A1');
      $table->dropColumn('highres_A2');
      $table->dropColumn('highres_A3');
    });
	}
}

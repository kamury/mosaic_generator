<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMosaicEventsAddHighres extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('mosaic_events', function($table)
    {
      $table->tinyInteger('highres_step')->nullable()->default(NULL)->comment('0 - need highres, 
                                                                          1 - part 1 ready, 
                                                                          2 - part 2 ready,
                                                                          3 - part 3 ready,
                                                                          4 - part 4 ready,
                                                                          5 - highres ready to download');
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
      $table->dropColumn('highres');
    });
	}

}

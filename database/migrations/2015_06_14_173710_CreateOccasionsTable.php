<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOccasionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('occasions', function (Blueprint $table) {
			$table->increments('id');
			$table->string('language');
			$table->integer('day');
			$table->integer('month');
			$table->integer('year')->nullable();
			$table->string('type');
			$table->string('fullDescription');
			$table->string('subject')->nullable();
			$table->string('subjectDetail')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('occasions');
	}

}

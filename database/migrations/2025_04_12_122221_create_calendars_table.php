<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('calendars', function(Blueprint $table) {
            $table->id()->comment('Rekord azonosító');

            $table->date('date')->index()->comment('Dátum');

            $table->timestamps();
            $table->softDeletes()->comment('Lágy törlés dátuma');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('calendars');
	}
};

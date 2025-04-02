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
		Schema::create('countries', function (Blueprint $table) {
            $table->id()->comment('Rekord azonosító');
            $table->string('name', 255)->index()->unique('name')->comment('Név');
            $table->string('code', 10)->index()->comment('Kód');

            $table->boolean('active')->default(1)->index()->comment('Aktív');

        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('countries');
	}
};

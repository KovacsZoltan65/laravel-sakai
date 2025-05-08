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
		Schema::create('worktime_limits', function(Blueprint $table) {
            $table->increments('id');
			$table->string('name', 255)->collation('utf8mb3_unicode_ci')->index()->comment('Név');

			$table->unsignedBigInteger('company_id')->index()->comment('Cég azonosító.');
			$table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

			$table->date('start_date')->comment('Kezdés dátuma');
			$table->date('end_date')->comment('Zárás dátuma');

			$table->boolean('active')->default(1)->index()->comment('Aktív');

            $table->timestamps();
			$table->softDeletes()->comment('Lágy törlés dátuma');

			$table->unique(['company_id', 'start_date', 'end_date'], 'uniq_company_period');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('worktime_limits');
	}
};

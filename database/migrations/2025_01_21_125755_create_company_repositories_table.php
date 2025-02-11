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
            Schema::create('company_repositories', function(Blueprint $table) {
                $table->increments('id');

                $table->string('name', 255);
                $table->string('email', 255);
                $table->string('address', 255);
                $table->string('phone', 255);
                
                $table->timestamps();
            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
            Schema::drop('company_repositories');
	}
};

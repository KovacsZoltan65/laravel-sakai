<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->id()->comment('Rekord azonosító');

            $table->string('name', 255)->collation('utf8mb3_unicode_ci')->index()->comment('Név');
            $table->string('email', 255)->collation('utf8mb3_unicode_ci')->index()->comment('Email cím');
            $table->timestamp('start_date')->comment('Belépés dátuma');
            $table->timestamp('end_date')->nullable()->comment('Kilépés dátuma');
            $table->timestamp('last_export')->nullable()->comment('Utoldó export');

            $table->unsignedBigInteger('user_id')->index()->comment('Személy azonosító.');
            $table->unsignedBigInteger('company_id')->index()->comment('Cég azonosító.');

            //$table->enum('active', [0,1])->default(1)->index()->comment('Aktív');
            $table->boolean('active')->default(1)->index()->comment('Aktív');

            $table->timestamps();
            $table->softDeletes()->comment('Lágy törlés dátuma');

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('entities');
    }
};

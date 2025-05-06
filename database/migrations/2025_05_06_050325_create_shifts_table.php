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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id()->comment('Rekord azonosító');
            $table->string('name', 255)->collation('utf8mb3_unicode_ci')->index()->comment('Név');
            $table->string('code', 8)->collation('utf8mb3_unicode_ci')->index()->comment('Kód');

            $table->time('start_time')->comment('Kezdő idöpont');
            $table->time('end_time')->comment('Záró idöpont');

            $table->unsignedBigInteger('company_id')->index()->comment('Cég azonosító.');
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            
            $table->boolean('active')->default(1)->index()->comment('Aktív');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};

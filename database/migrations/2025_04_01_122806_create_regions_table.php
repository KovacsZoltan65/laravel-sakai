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
        Schema::create('regions', function (Blueprint $table) {
            $table->id()->comment('Rekord azonosító. Egyedi azonosító a rekordhoz.');
            $table->string('name', 255)->index()->comment('Név. A régió neve.');
            $table->string('code', 10)->index()->comment('Kód. A régió kódja.');

            $table->unsignedBigInteger('country_id')->comment('Ország azonosító. A kapcsolódó ország azonosítója.');

            //$table->enum('active', [0,1])->default(1)->index()->comment('Aktív');
            $table->boolean('active')->default(1)->index()->comment('Aktív');

            $table->foreign('country_id')->references('id')->on('countries')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};

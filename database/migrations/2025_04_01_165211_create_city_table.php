<?php

use App\Models\Country;
use App\Models\Region;
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
        Schema::create('cities', function (Blueprint $table) {

            $table->id()->comment('Rekord azonosító. Egyedi azonosító a rekordhoz.');
            $table->decimal('latitude', 10, 2)->nullable()->comment('Szélesség. A város szélességi koordinátája.');
            $table->decimal('longitude', 10, 2)->nullable()->comment('Hosszúság. A város hosszúsági koordinátája.');
            $table->string('name', 255)->index()->comment('Név. A város neve.');

            $table->unsignedBigInteger('country_id')->comment('Ország azonosító. A kapcsolódó ország azonosítója.');
            $table->foreign('country_id')->references('id')->on('countries')->cascadeOnDelete();
            
            $table->unsignedBigInteger('region_id')->comment('Régió azonosító. A kapcsolódó régió azonosítója.');
            $table->foreign('region_id')->references('id')->on('regions')->cascadeOnDelete();
            //$table->unsignedBigInteger('city_id')->comment('Város azonosító. A kapcsolódó megye / régió azonosítója.');

            //$table->enum('active', [0,1])->default(1)->index()->comment('Aktív');
            $table->boolean('active')->default(1)->index()->comment('Aktív');

            
            
            //$table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};

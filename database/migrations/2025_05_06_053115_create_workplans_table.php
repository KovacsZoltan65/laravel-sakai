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
        Schema::create('workplan', function (Blueprint $table) {
            $table->id()->comment('Rekord azonosító');
            $table->string('name', 255)->collation('utf8mb3_unicode_ci')->index()->comment('Név');
            $table->string('code', 8)->collation('utf8mb3_unicode_ci')->index()->comment('Kód');

            $table->unsignedBigInteger('company_id')->index()->comment('Cég azonosító.');
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->date('start_date')->comment('Kezdés dátuma');
            $table->date('end_date')->comment('Zárás dátuma');

            $table->boolean('active')->default(1)->index()->comment('Aktív');

            $table->timestamps();
            $table->SoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workplan');
    }
};

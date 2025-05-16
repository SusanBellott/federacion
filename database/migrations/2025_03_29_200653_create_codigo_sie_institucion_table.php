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
        Schema::create('codigo_sie_institucion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('codigo_sie_id');
            $table->unsignedBigInteger('institucion_id');
            $table->timestamps();

            // Relaciones
            $table->foreign('codigo_sie_id')->references('id_codigo_sie')->on('codigo_sies')->onDelete('cascade');
            $table->foreign('institucion_id')->references('id_institucion')->on('instituciones')->onDelete('cascade');
            
            // Un código SIE solo puede estar asociado a una institución una vez
            $table->unique(['codigo_sie_id', 'institucion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codigo_sie_institucion');
    }
};

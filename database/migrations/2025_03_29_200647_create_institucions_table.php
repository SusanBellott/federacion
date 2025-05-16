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
        Schema::create('instituciones', function (Blueprint $table) {
            $table->id('id_institucion');
            $table->uuid('uuid_institucion');
            $table->unsignedBigInteger('id_distrito');
            $table->unsignedBigInteger('unidad_educativa_id');
            $table->bigInteger('servicio')->unique();
            $table->integer('servicio_generado');
            $table->string('subsistema');
            $table->string('nivel');
            $table->string('estado')->default('activo');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('id_distrito')->references('id_distrito')->on('distritos')->onDelete('cascade');
            $table->foreign('unidad_educativa_id')->references('id_codigo_sie')->on('codigo_sies')->onDelete('cascade');

        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institucions');
    }
};

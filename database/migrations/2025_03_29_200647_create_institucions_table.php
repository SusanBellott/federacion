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
        Schema::create('institucions', function (Blueprint $table) {
            $table->id('id_institucion');
            $table->unsignedBigInteger('id_distrito');
            $table->uuid('uuid_institucion');
            $table->string('subsistema');
            $table->bigInteger('servicio');
            $table->Integer('servicio_generado');
            $table->string('nivel');
            $table->bigInteger('programa');
            $table->string('unidad_educativa');
            $table->string('estado')->default('activo');
            $table->foreign('id_distrito')->references('id_distrito')->on('distritos')->onDelete('cascade');


            $table->timestamps();
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

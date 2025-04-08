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
        Schema::create('cursos', function (Blueprint $table) {
            $table->id('id_curso');
            $table->uuid('uuid_curso');
            $table->string('nombre');
            $table->string('descripcion');
            $table->string('fecha_inicio')->default(date('Y-m-d'));
            $table->string('fecha_fin')->default(date('Y-m-d'));
            $table->integer('carga_horaria');
            $table->string('img_curso')->default('curso.jpg');
            $table->string('encargado')->nullable();
            $table->string('grado_academico')->nullable();
            $table->string('estado_curso')->default('');
            $table->string('titulocer')->nullable();
            $table->string('contenidocer')->nullable();
            $table->string('img_cer')->nullable();
            $table->string('img_cerlogo')->nullable();
            $table->string('img_firma1')->nullable();
            $table->string('img_firma2')->nullable();
            $table->string('img_firma3')->nullable();
            $table->string('img_firma4')->nullable();
            $table->string('img_sello')->nullable();
            $table->string('estado')->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};

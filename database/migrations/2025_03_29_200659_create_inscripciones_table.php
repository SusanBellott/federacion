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
        Schema::create('inscripcions', function (Blueprint $table) {
            $table->id('id_inscripcion');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_curso');  // Permite que id_curso sea null
            $table->uuid('uuid_inscripcion');
            $table->string('fecha_inscripcion')->default(date('Y-m-d'));
            $table->string('estado_ins')->default('');
            $table->string('estado')->default('activo');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_curso')->references('id_curso')->on('cursos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripcions');
    }
};

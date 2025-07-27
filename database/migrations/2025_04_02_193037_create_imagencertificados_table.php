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
        Schema::create('imagencertificados', function (Blueprint $table) {
            $table->id('id_imgcer');
            $table->uuid('uuid_imgcer');
            $table->unsignedBigInteger('id_curso');
            $table->string('imagenescer')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('estado')->default('activo');
            $table->foreign('id_curso')->references('id_curso')->on('cursos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imagencertificados');
    }
};

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
        Schema::create('codigo_sies', function (Blueprint $table) {
            $table->id('id_codigo_sie');
            $table->uuid('uuid_codigo_sie');
            $table->bigInteger('programa')->unique();;
            $table->string('unidad_educativa');
            $table->string('estado')->default('activo');
            $table->timestamps();
            $table->softDeletes();
// Relación con distrito
$table->unsignedBigInteger('distrito_id');
$table->foreign('distrito_id')->references('id_distrito')->on('distritos')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codigo_sies');
    }
};

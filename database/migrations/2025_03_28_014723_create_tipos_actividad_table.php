<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tipos_actividad', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_tipo_actividad');
            $table->string('codigo')->unique(); //  Código tipo CR-001
            $table->string('nombre');           //  Ya no único
            $table->integer('horas_minimas')->default(2); // Mínimo 2 horas
            $table->string('estado')->default('activo');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_actividad');
    }
};

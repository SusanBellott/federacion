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
        Schema::table('inscripcions', function (Blueprint $table) {  // ← plural en inglés
            $table->unsignedInteger('certificado_numero')
                ->nullable()
                ->after('uuid_inscripcion');
        });
    }

    public function down(): void
    {
        Schema::table('inscripcions', function (Blueprint $table) {
            $table->dropColumn('certificado_numero');
        });
    }
};

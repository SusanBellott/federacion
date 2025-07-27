<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Renombramos columnas para conservar datos
        Schema::table('cursos', function ($table) {
            $table->renameColumn('fecha_inicio', 'fecha_inicio_str');
            $table->renameColumn('fecha_fin', 'fecha_fin_str');
        });

        // Creamos nuevas columnas tipo date
        Schema::table('cursos', function ($table) {
            $table->date('fecha_inicio')->nullable()->after('fecha_inicio_str');
            $table->date('fecha_fin')->nullable()->after('fecha_fin_str');
        });

        // Copiamos datos convertidos
        DB::statement("UPDATE cursos SET fecha_inicio = fecha_inicio_str::date");
        DB::statement("UPDATE cursos SET fecha_fin = fecha_fin_str::date");

        // (Opcional) Elimina las columnas antiguas tipo string si ya no las necesitas:
        Schema::table('cursos', function ($table) {
            $table->dropColumn('fecha_inicio_str');
            $table->dropColumn('fecha_fin_str');
        });
    }

    public function down(): void
    {
        Schema::table('cursos', function ($table) {
            $table->string('fecha_inicio_str')->nullable();
            $table->string('fecha_fin_str')->nullable();
        });

        DB::statement("UPDATE cursos SET fecha_inicio_str = fecha_inicio::text");
        DB::statement("UPDATE cursos SET fecha_fin_str = fecha_fin::text");

        Schema::table('cursos', function ($table) {
            $table->dropColumn('fecha_inicio');
            $table->dropColumn('fecha_fin');
            $table->renameColumn('fecha_inicio_str', 'fecha_inicio');
            $table->renameColumn('fecha_fin_str', 'fecha_fin');
        });
    }
};

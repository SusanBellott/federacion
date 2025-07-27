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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_user');
            $table->bigInteger('ci');
            $table->string('complemento_ci', 5)->nullable(); // ← nuevo campo
            $table->bigInteger('rda')->unique();;
            $table->string('name');
            $table->string('primer_apellido')->nullable();
            $table->string('segundo_apellido')->nullable();
            $table->bigInteger('item')->nullable();
            $table->bigInteger('cargo')->nullable();
            $table->integer('horas')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->string('estado')->default('activo');
            $table->timestamps();
            $table->softDeletes();

            // Restricción compuesta única (muy importante)
            $table->unique(['ci', 'complemento_ci'], 'users_ci_complemento_unique');

            // Relaciones correctas con las tablas
            $table->unsignedBigInteger('distrito_id')->nullable();
            $table->unsignedBigInteger('institucion_id')->nullable();
            $table->unsignedBigInteger('codigo_sie_id')->nullable();

            // Referencias correctas a las columnas ID específicas
            $table->foreign('distrito_id')->references('id_distrito')->on('distritos')->onDelete('set null');
            $table->foreign('institucion_id')->references('id_institucion')->on('instituciones')->onDelete('set null');
            $table->foreign('codigo_sie_id')->references('id_codigo_sie')->on('codigo_sies')->onDelete('set null');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();

            // Informações principais
            $table->string('name')->comment('Nome do card');
            $table->text('description')->comment('Descrição detalhada do card');
            $table->text('image_path')->comment('Caminho da imagem do card');
            $table->text('url')->comment('URL associada ao card');

            // Tipo do card (exemplo: dashboard, relatório, etc.)
            $table->string('type')->default('dashboard')->comment('Tipo do card, ex: dashboard, relatório');

            // Controle de registros
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};

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
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();

            // Informações laboratoriais
            $table->boolean('lab_responsible')->default(false)->comment('Indica se há um responsável pelo laboratório');
            $table->longText('lab_notes')->nullable()->comment('Notas do laboratório');
            $table->longText('unit_notes')->nullable()->comment('Notas da unidade');

            // Lista de medicamentos analisados
            $table->json('medications')->nullable()->comment('Lista de medicamentos envolvidos na análise');

            // Relacionamento com o usuário criador
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->default(1)
                ->comment('Usuário que criou o registro');

            // Controle de registros
            $table->timestamps();
            $table->softDeletes()->comment('Marca o registro como excluído sem removê-lo definitivamente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};

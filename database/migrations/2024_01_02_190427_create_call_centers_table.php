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
        Schema::create('call_centers', function (Blueprint $table) {
            $table->id();
            $table->string('protocolo');
            $table->string('setor');
            $table->string('unidade')->nullable();
            $table->json('medicamentos')->nullable();
            $table->string('resp_aquisicao')->nullable();
            $table->string('demandante')->nullable();
            $table->boolean('dado_sigiloso')->default(false);
            $table->text('file_espelho')->nullable();
            $table->json('attachments')->nullable();
            $table->longText('obs')->nullable();
            $table->string('date_dispensacao')->nullable();
            $table->string('date_resposta')->nullable();
            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_centers');
    }
};

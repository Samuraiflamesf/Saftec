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
            $table->string('demandante')->nullable();
            $table->boolean('dado_sigiloso')->default(false);
            $table->string('unidade')->nullable();
            $table->string('resp_aquisicao')->nullable();
            $table->string('date_dispensacao')->nullable();
            $table->json('medicamentos')->nullable();
            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->string('date_resposta')->nullable();
            $table->longText('obs')->nullable();
            $table->text('file_espelho')->nullable();
            $table->json('attachments')->nullable();
            $table->foreignId('user_create_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->default(1);
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

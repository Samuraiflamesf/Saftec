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
        Schema::create('stability_consultations', function (Blueprint $table) {
            $table->id();
            $table->string('institution_name'); // Nome da Instituição
            $table->string('cnpj', 18); // CNPJ
            $table->timestamp('last_verification_at')->nullable(); // Última verificação antes da excursão de temperatura
            $table->timestamp('excursion_verification_at')->nullable(); // Verificação da excursão de temperatura
            $table->integer('estimated_exposure_time')->nullable(); // Tempo de exposição
            $table->timestamp('returned_to_storage_at')->nullable(); // Item retornou ao armazenamento
            $table->decimal('max_exposed_temperature', 5, 2)->nullable(); // Temperatura máxima exposta
            $table->decimal('min_exposed_temperature', 5, 2)->nullable(); // Temperatura mínimo exposta
            // Repeater
            $table->json('medicament')->nullable(); // nome do medicamento
            $table->string('order_number'); // Nº do pedido
            $table->string('distribution_number'); // Nº da distribuição
            $table->text('observations')->nullable(); // Observações
            $table->text('file_monitor_temp')->nullable();
            // Keys automatically
            $table->string('protocol_number')->unique();
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
        Schema::dropIfExists('stability_consultations');
    }
};

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
        Schema::table('stability_consultations', function (Blueprint $table) {
            $table->foreignId('estabelecimento_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stability_consultations', function (Blueprint $table) {
            $table->dropForeign(['estabelecimento_id']);
            $table->dropColumn('estabelecimento_id');
        });
    }
};

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
            $table->text('local_exposure')->nullable();
            $table->boolean('boolean_unit')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stability_consultations', function (Blueprint $table) {
            $table->dropColumn(['local_exposure', 'boolean_unit']);
        });
    }
};

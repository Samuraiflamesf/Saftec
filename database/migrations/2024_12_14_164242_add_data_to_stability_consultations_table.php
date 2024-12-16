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
            $table->boolean('resp_laboratory')->default(false);
            $table->text('text_laboratory')->nullable();
            $table->text('text_unidade')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stability_consultations', function (Blueprint $table) {
            //
        });
    }
};

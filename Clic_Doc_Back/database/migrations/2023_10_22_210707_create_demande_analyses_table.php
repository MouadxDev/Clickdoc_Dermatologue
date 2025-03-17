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
        Schema::create('demande_analyses', function (Blueprint $table) {
            $table->id();
            $table->integer("consultation_id");
            $table->integer("analyse_id");
            $table->integer("lab_id")->nullable();
            $table->string("state");
            $table->string("document")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_analyses');
    }
};

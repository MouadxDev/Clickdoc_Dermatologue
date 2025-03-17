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
        Schema::create('tableau_personalises', function (Blueprint $table) {
            $table->id();
            $table->string("date");
            $table->string("indication");
            $table->string("laser");
            $table->string("pore")->default("0");
            $table->string("longueur_onde")->default("0");
            $table->string("pm")->default("0");
            $table->string("fu")->default("0");
            $table->string("lt")->default("0");
            $table->string("Note");
            $table->integer("patient_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tableau_personalises');
    }
};

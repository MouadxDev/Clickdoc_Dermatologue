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
        Schema::create('examen_physiques', function (Blueprint $table) {
            $table->id();
            $table->integer("consultation_id");
            $table->string("nails")->nullable()->default("[]");
            $table->string("hair")->nullable()->default("[]");
            $table->string("face")->nullable()->default("[]");
            $table->string("body")->nullable()->default("[]");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examen_physiques');
    }
};

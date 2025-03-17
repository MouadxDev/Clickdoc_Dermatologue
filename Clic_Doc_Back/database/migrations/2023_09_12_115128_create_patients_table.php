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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string("uid")->nullable();
            $table->string("sex");
            $table->string("name");
            $table->string("surname");
            $table->string("date_of_birth") -> nullable();
            $table->string("phone");
            $table->integer("diabetes")->nullable();
            $table->string("blood_type")->nullable();
            $table->string("CIN")->nullable()->unique();
            $table->string("avatar")->default("avatar.png");
            $table->boolean("coverage")->default(false);
            $table->string("coverage_type")->nullable();
            $table->string("observation")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};

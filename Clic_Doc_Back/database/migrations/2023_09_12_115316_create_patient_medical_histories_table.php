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
        Schema::create('patient_medical_histories', function (Blueprint $table) {
            $table->id();
            $table->boolean("chronic");
            $table->boolean("cardiac");
            $table->boolean("lung");
            $table->boolean("kidney");
            $table->boolean("cancer");
            $table->boolean("nerves");
            $table->boolean("gastric");
            $table->string("chronic_comment")->nullable();
            $table->string("cardiac_comment")->nullable();
            $table->string("lung_comment")->nullable();
            $table->string("kidney_comment")->nullable();
            $table->string("cancer_comment")->nullable();
            $table->string("nerves_comment")->nullable();
            $table->string("gastric_comment")->nullable();
            $table->integer("patient_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_medical_histories');
    }
};

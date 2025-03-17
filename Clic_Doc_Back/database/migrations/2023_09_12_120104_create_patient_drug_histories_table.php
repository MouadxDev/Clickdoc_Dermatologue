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
        Schema::create('patient_drug_histories', function (Blueprint $table) {
            $table->id();
            $table->integer("patient_id");
            $table->string("name");
            $table->string("dose");
            $table->string("frequency");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_drug_histories');
    }
};

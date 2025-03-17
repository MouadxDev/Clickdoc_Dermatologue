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
        Schema::create('entites', function (Blueprint $table) {
            $table->id();
            $table->boolean("licence");
            $table->string("creation");
            $table->string("expiration") -> nullable();
            $table->string("type") -> default("cabinet");
            $table->integer("renewal");
            $table->integer("payment_cycle");
            $table->string("logo") -> nullable();
            $table->string("name");
            $table->string("adress");
            $table->string("city");
            $table->string("contact_email")->unique();
            $table->string("contact_name");
            $table->boolean("used_trial");
            $table->string("justification")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entites');
    }
};

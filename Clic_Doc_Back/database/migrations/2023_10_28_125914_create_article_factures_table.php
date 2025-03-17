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
        Schema::create('article_factures', function (Blueprint $table) {
            $table->id();
            $table->integer("facture_id");
            $table->string("libelle");
            $table->double("prix");
            $table->integer("type"); //  1 - frais consultation  ; 2 - Actes Médicaux  ; 3- Autres
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_factures');
    }
};

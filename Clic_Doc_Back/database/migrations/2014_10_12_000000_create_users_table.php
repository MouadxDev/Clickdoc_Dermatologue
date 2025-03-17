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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->integer('entity_id')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string("mobile_number")->nullable();
            $table->string("avatar")->default("avatar.png");
            $table->string("role");
            $table->string("address");
            $table->string("city");
            $table->string("specialty")->nullable();
            $table->double("experience")->default(1);
            $table->string("license_number")->nullable();
            $table->string("degree")->nullable();
            $table->string("gender")->nullable();
            $table->string("about")->nullable();
            $table->double("fee")->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

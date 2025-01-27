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
        Schema::create('republics', function (Blueprint $table) {
            $table->id();
            $table->string('responsible_office',1000)->nullable();
            $table->foreignId('bohol_issuance_id')->constrained('bohol_issuances')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('republics');
    }
};

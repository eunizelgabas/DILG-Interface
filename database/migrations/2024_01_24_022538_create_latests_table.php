<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('latests', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable();
            $table->string('outcome');
            $table->bigInteger('issuance_id')->unsigned();
            $table->foreign('issuance_id')->references('id')->on('issuances')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('latests');
    }
};

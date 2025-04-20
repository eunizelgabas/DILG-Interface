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
        Schema::create('republic_acts', function (Blueprint $table) {
            $table->id();
            $table->text('title')->nullable();
            $table->text('link')->nullable();
            $table->text('reference')->unique();
            $table->text('date')->nullable();
            $table->string('type')->nullable();
            $table->text('download_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('republic_acts');
    }
};

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
        Schema::create('legal_opinions_pdfs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legal_opinion_id')->constrained('legal_opinions')->onDelete('cascade'); // Foreign key
            $table->binary('pdf_file')->nullable(); // Store the PDF as binary (optional)
            $table->string('file_path')->nullable(); // Path if stored in storage
            $table->longText('extracted_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_opinions_pdfs');
    }
};

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
        Schema::create('legal_opinions', function (Blueprint $table) {
            $table->id();
            $table->text('title')->nullable();
            $table->text('link')->nullable();
            $table->text('category')->nullable();
            $table->text('reference')->unique();
            $table->text('date')->nullable();
            $table->text('download_link')->nullable();
            $table->longText('extracted_texts')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_opinions');
    }
};

// <?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.  
//      */
//     public function up(): void
//     {
//         Schema::create('legal_opinions', function (Blueprint $table) {
//             $table->id();
//             $table->string('category')->nullable();
//             $table->bigInteger('issuance_id')->unsigned();
//             $table->foreign('issuance_id')->references('id')->on('issuances')->onDelete('cascade');
//             $table->timestamps();
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('legals');
//     }
// };

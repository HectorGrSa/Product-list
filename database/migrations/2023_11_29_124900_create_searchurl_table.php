<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Creacion de la tabla "searchURL"
    public function up(): void
    {
        Schema::create('searchurls', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('searchURL');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('searchurl');
    }
};


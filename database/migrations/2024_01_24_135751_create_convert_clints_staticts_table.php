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
        Schema::create('convert_clints_staticts', function (Blueprint $table) {
            $table->id();
            $table->string('numberOfClients')->nullable();
            $table->dateTime('convert_date')->nullable();
            $table->unsignedBigInteger('oldUserId')->nullable();
            $table->unsignedBigInteger('newUserId')->nullable();
            $table->string('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convert_clints_staticts');
    }
};

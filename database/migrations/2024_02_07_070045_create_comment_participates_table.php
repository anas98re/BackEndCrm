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
        Schema::create('comment_participates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('participate_id')->nullable();
            $table->text('content')->nullable();
            $table->date('date_comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_participates');
    }
};

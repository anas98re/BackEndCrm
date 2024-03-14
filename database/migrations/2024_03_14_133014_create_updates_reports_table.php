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
        Schema::create('updates_reports', function (Blueprint $table) {
            $table->id();
            $table->text('changesData')->nullable();
            $table->string('model')->nullable();
            $table->integer('model_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->dateTime('edit_date')->nullable();
            $table->string('source')->nullable();
            $table->text('description')->nullable();
            $table->enum('afterApprove', ['true', 'false'])->default('false')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('updates_reports');
    }
};

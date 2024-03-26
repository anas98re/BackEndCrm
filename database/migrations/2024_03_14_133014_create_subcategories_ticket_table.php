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
        Schema::create('subcategories_ticket', function (Blueprint $table) {
            $table->id();
            $table->string('sub_category_ar')->nullable();
            $table->string('sub_category_en')->nullable();
            $table->string('classification')->nullable();
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

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
        Schema::create('invoices_update_reports', function (Blueprint $table) {
            $table->id();
            $table->text('changesData')->nullable();
            $table->enum('afterApprove', ['true', 'false'])->default('false')->nullable();
            $table->dateTime('edit_date')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices_update_reports');
    }
};

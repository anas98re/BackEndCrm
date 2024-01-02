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
        Schema::create('Privilages_report', function (Blueprint $table) {
            $table->id();
            $table->string('privilage_name')->nullable();
            $table->dateTime('edit_date')->nullable();
            $table->string('user_update_name')->nullable();
            $table->string('update_to')->nullable();

            $table->timestamps();
        });
    }

    /**u
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_comments');
    }
};

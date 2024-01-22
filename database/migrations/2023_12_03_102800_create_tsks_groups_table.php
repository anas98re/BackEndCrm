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
        Schema::create('tasks_groups', function (Blueprint $table) {
            $table->id();
            $table->string('groupName');
            $table->text('description');
            $table->unsignedInteger('created_by');
            $table->timestamps();

            $table->foreign('created_by')->references('id_user')->on('users')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasksGroups');
    }
};

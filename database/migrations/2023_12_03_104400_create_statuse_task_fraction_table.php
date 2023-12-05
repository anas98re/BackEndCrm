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
        Schema::create('statuse_task_fraction', function (Blueprint $table) {
            $table->id();
            $table->dateTime('changed_date')->nullable();
            $table->unsignedBigInteger('task_statuse_id')->nullable();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('changed_by');
            $table->timestamps();

            $table->foreign('task_statuse_id')->references('id')->on('task_statuses')->onDelete('set null');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            // $table->foreign('changed_by')->references('id_user')->on('users')->onDelete('set null');

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

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
        Schema::create('task_attachments', function (Blueprint $table) {
            $table->id();
            $table->dateTime('create_date')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            // $table->foreign('created_by')->references('id_user')->on('users')->onDelete('set null');
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('change_logs', function (Blueprint $table) {
            $table->id();
            $table->string('model')->nullable();
            $table->string('action')->nullable();
            $table->text('old_data')->nullable();
            $table->text('new_data')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('model_name')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('route')->nullable();
            $table->string('ip')->nullable();
            $table->timestamps();

            // $table->foreign('user_id')->references('id')->on('users');
            // Modify 'users' table to match your actual users table name

            $table->index(['model', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('change_logs');
    }
};

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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->enum('public_Type', ['fieldTask', 'ُEdit'])->default('fieldTask');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->integer('hours')->nullable();
            $table->integer('completion_percentage')->default(0);
            $table->boolean('recurring')->default(false);
            $table->enum('recurring_type', ['daily', 'weekly','monthly','other'])->nullable();
            $table->string('Number_Of_Recurring')->nullable();
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('tasks_groups')->onDelete('set null');
            // $table->foreign('assigned_by')->references('id_user')->on('users')->onDelete('set null');
            // $table->foreign('assigned_to')->references('id_user')->on('users')->onDelete('set null');
            // $table->foreign('invoice_id')->references('id_invoice ')->on('client_invoice')->onDelete('set null');
            // $table->foreign('client_id')->references('id_clients')->on('clients')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

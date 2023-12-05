<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('task_statuses', function (Blueprint $table) {
            $table->id();
            $table->enum(
                'name',
                [
                    'Pending', 'Completed', 'Testing', 'InProgress',
                    'InReview', 'Accepted', 'Rejected', 'Blocked'
                ]
            )
                ->default('InProgress');
            $table->enum('type', ['Task', 'Bug', 'Feature'])->default('Task');
            $table->enum('priority', ['Low', 'Medium', 'High'])->default('Medium');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_statuses');
    }
};

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
        Schema::create('todos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable(false)
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->string('title', 40)
                ->index('idx_todos_title')
                ->nullable(false);

            $table->text('description');

            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');

            $table->boolean('is_completed')
                ->default(false)
                ->nullable(false);

            $table->dateTime('due_at');
            $table->dateTime('completed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};

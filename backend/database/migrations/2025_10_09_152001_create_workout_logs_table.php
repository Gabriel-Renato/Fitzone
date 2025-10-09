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
        Schema::create('workout_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('workout_id')->constrained()->onDelete('cascade');
            $table->foreignId('workout_plan_id')->nullable()->constrained()->onDelete('set null');
            $table->date('completed_at'); // Data que o treino foi realizado
            $table->integer('duration')->nullable(); // Duração em minutos
            $table->text('notes')->nullable(); // Observações do cliente
            $table->json('exercises_completed')->nullable(); // Array de exercícios concluídos com detalhes
            $table->integer('rating')->nullable(); // Avaliação do treino (1-5)
            $table->timestamps();
            
            // Índices para consultas rápidas
            $table->index(['user_id', 'completed_at']);
            $table->index('workout_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_logs');
    }
};
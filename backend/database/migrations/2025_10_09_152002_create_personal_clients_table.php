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
        Schema::create('personal_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['ativo', 'inativo', 'pendente'])->default('ativo');
            $table->date('start_date')->default(now());
            $table->date('end_date')->nullable();
            $table->text('goals')->nullable(); // Objetivos do cliente
            $table->text('observations')->nullable(); // Observações do personal
            $table->timestamps();
            
            // Garantir que um cliente não tenha múltiplos personals ativos
            $table->unique(['client_id', 'status']);
            $table->index('personal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_clients');
    }
};
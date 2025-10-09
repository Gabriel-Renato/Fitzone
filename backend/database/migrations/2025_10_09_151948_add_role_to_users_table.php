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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['personal', 'cliente'])->default('cliente')->after('email');
            $table->foreignId('personal_id')->nullable()->after('role')->constrained('users')->onDelete('set null');
            $table->string('phone')->nullable()->after('email');
            $table->text('bio')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['personal_id']);
            $table->dropColumn(['role', 'personal_id', 'phone', 'bio', 'avatar']);
        });
    }
};
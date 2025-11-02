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
            // Adicionar campos de perfil se não existirem
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'cpf')) {
                $table->string('cpf')->nullable()->unique()->after('phone');
            }
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('cpf');
            }
            if (!Schema::hasColumn('users', 'profession')) {
                $table->string('profession')->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('profession');
            }
            if (!Schema::hasColumn('users', 'avatar_url')) {
                $table->string('avatar_url')->nullable()->after('bio');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumnIfExists('phone');
            $table->dropColumnIfExists('cpf');
            $table->dropColumnIfExists('date_of_birth');
            $table->dropColumnIfExists('profession');
            $table->dropColumnIfExists('bio');
            $table->dropColumnIfExists('avatar_url');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateContasTableAddCompositeUnique extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contas', function (Blueprint $table) {
            // Removendo a restrição de unicidade existente no nome
            $table->dropUnique('contas_name_unique');
            
            // Adicionando a nova restrição de unicidade composta
            $table->unique(['name', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contas', function (Blueprint $table) {
            // Removendo a restrição de unicidade composta
            $table->dropUnique(['name', 'user_id']);
            
            // Restaurando a restrição de unicidade original no nome
            $table->unique('name');
        });
    }
}
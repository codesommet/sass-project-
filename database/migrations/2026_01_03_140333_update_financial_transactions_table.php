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
        Schema::table('financial_transactions', function (Blueprint $table) {
            // Renommer les colonnes related_type/related_id en source_type/source_id
            if (Schema::hasColumn('financial_transactions', 'related_type')) {
                $table->renameColumn('related_type', 'source_type');
            }
            if (Schema::hasColumn('financial_transactions', 'related_id')) {
                $table->renameColumn('related_id', 'source_id');
            }
            
            // Ajouter la colonne metadata si elle n'existe pas
            if (!Schema::hasColumn('financial_transactions', 'metadata')) {
                $table->json('metadata')->nullable()->after('source_id');
            }
            
            // Ajouter des index
            $table->index(['source_type', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->dropIndex(['source_type', 'source_id']);
            
            if (Schema::hasColumn('financial_transactions', 'source_type')) {
                $table->renameColumn('source_type', 'related_type');
            }
            if (Schema::hasColumn('financial_transactions', 'source_id')) {
                $table->renameColumn('source_id', 'related_id');
            }
            
            if (Schema::hasColumn('financial_transactions', 'metadata')) {
                $table->dropColumn('metadata');
            }
        });
    }
};
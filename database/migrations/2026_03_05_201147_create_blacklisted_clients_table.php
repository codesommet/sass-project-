<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blacklisted_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('agency_id')->constrained('agencies')->onDelete('cascade');
            $table->foreignId('blacklisted_by')->constrained('users')->onDelete('cascade');
            $table->text('reason');
            $table->text('internal_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['client_id', 'agency_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blacklisted_clients');
    }
};
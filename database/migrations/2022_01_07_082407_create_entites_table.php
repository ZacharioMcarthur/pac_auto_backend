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
        Schema::create('entites', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code')->unique();
            
            // Type d'entité (hiérarchique)
            $table->enum('type', ['direction', 'departement', 'service', 'bureau']);
            
            // Clé étrangère vers la table elle-même (auto-relation hiérarchique)
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('entites')
                ->onDelete('cascade');

            $table->timestamps();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entites');
    }
};

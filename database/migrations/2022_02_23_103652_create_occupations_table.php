<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOccupationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('occupations', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_depart');
            $table->dateTime('date_retour');

            $table->foreignId('vehicule_id')->constrained('vehicules')->cascadeOnDelete();
            $table->foreignId('demande_vehicule_id')->constrained('demande_vehicules')->cascadeOnDelete();
            $table->foreignId('chauffeur_id')->constrained('chauffeurs')->cascadeOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('occupations');
    }
}

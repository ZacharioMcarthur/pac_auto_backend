<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgrammersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programmation', function (Blueprint $table) {
        $table->id();
        
        // Utilisation de unsignedBigInteger pour correspondre aux tables parentes
        $table->unsignedBigInteger('chauffeur_id');
        $table->unsignedBigInteger('planning_garde_id');
        
        $table->dateTime('date_fin_repos');
        $table->timestamps();

        // Définition des clés étrangères
        $table->foreign('chauffeur_id')->references('id')->on('chauffeurs')->onDelete('cascade');
        $table->foreign('planning_garde_id')->references('id')->on('planning_gardes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programmation');
    }
}

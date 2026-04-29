<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandeVehiculesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demande_vehicules', function (Blueprint $table) {
            $table->id(); 
            $table->string('reference')->nullable();
            $table->string('objet');
            $table->dateTime('date_depart');
            $table->dateTime('date_retour');
            $table->dateTime('date_depart_effectif')->nullable();
            $table->dateTime('date_retour_effectif')->nullable();
            $table->string('point_depart');
            $table->string('point_destination');
            $table->integer('nbre_personnes');
            $table->string('statut');
            $table->boolean('is_note')->default(false);
            $table->string('escales')->nullable();

            // Modification : Remplacer unsignedInteger par unsignedBigInteger
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('motif_id');
            $table->unsignedBigInteger('type_vehicule_id');
            $table->unsignedBigInteger('chauffeur_id')->nullable(); // nullable si le chauffeur n'est pas assigné de suite
            // $table->unsignedBigInteger('vehicule_id')->nullable();  // nullable si le véhicule n'est pas assigné de suite

            // Clés étrangères
            $table->foreign('type_vehicule_id')->references('id')->on('type_vehicules');
            $table->foreign('motif_id')->references('id')->on('motifs');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('chauffeur_id')->references('id')->on('chauffeurs');
            // $table->foreign('vehicule_id')->references('id')->on('vehicules');
            
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
        Schema::dropIfExists('demande_vehicules');
    }
}

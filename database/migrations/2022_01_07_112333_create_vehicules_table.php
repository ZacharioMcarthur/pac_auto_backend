<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiculesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicules', function (Blueprint $table) {
            $table->id();
            $table->string('immatr');
            $table->string('marque');
            $table->string('nomMembre')->nullable();
            $table->string('date_mise_circulation');
            $table->string('disponibilite');
            $table->integer('capacite');
            $table->boolean('statut');
            

            // Ajout des colonnes pour les clés étrangères manquantes, Utiliser unsignedBigInteger partout où l'ID pointé est un BigInt
            $table->unsignedBigInteger('type_vehicule_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            

            $table->timestamps();

            //Définition des contraintes
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('type_vehicule_id')->references('id')->on('type_vehicules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicules');
    }
}

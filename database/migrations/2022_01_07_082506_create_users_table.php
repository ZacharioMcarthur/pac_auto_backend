<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Remplace increments() par id() (bigIncrements par défaut)
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('tel');
            $table->string('statut');
            $table->string('password');

            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('categorie_user_id');

            // Ajout de la colonne entite_id pour la relation avec la table entites
            $table->unsignedBigInteger('entite_id')->nullable();

            $table->rememberToken();
            $table->timestamps();

            // Clés étrangères
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('categorie_user_id')->references('id')->on('categorie_users')->onDelete('cascade');
            $table->foreign('entite_id')->references('id')->on('entites')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

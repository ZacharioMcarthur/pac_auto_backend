<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConduiteDemandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conduite_demandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_permis_id')->constrained('categorie_permis')->cascadeOnDelete();
            $table->foreignId('vehicule_id')->constrained('vehicules')->cascadeOnDelete();
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
        Schema::dropIfExists('conduite_demandes');
    }
}

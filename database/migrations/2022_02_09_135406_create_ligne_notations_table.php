<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLigneNotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ligne_notations', function (Blueprint $table) {
            $table->id();;
            $table->foreignId('notation_id')->constrained('notations')->cascadeOnDelete();
            $table->foreignId('critere_notation_id')->constrained('critere_notations')->cascadeOnDelete();
            $table->foreignId('chauffeur_id')->constrained('chauffeurs')->cascadeOnDelete();
            $table->integer('valeur');
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
        Schema::dropIfExists('ligne_notations');
    }
}

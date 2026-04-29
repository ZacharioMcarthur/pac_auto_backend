<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_sms', function (Blueprint $table) {
            $table->id();
            $table->string('contact');
            $table->string('contenu');
            // $table->string('status_envoi');
            $table->enum('status_envoi',[env('SMS_CREE'), env('SMS_ENVOYE')]);
            $table->dateTime('date_envoi');
            $table->timestamps();
            
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_sms');
    }
}

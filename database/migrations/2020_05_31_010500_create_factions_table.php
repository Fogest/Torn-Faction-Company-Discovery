<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'factions', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->primary()->comment('The factions in game ID from Torn');
                $table->string('name');
                $table->integer('current_players');
                $table->integer('max_players');
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('factions');
    }
}

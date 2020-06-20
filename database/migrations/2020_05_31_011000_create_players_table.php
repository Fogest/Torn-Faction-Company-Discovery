<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'players', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->primary()->comment('The players in game ID from Torn');
                $table->unsignedBigInteger('faction_id');
                $table->string('name', 150);
                $table->timestamp('last_complete_update_at')->useCurrent();
                $table->timestamps();

                $table->foreign('faction_id')
                    ->references('id')
                    ->on('factions')
                    ->onDelete('cascade');
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
        Schema::dropIfExists('players');
    }
}

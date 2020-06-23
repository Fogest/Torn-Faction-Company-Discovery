<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerRecruitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_recruits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id')->comment('Players torn id');
            $table->unsignedBigInteger('faction_id')->comment('Players faction id')->nullable();
            $table->unsignedBigInteger('recruited_by_id')->nullable();
            $table->string('player_name', 150);
            $table->string('faction_name', 200)->nullable();
            $table->string('recruited_by', 200);
            $table->boolean('is_required_stats')->nullable();
            $table->boolean('is_accepted')->nullable();
            $table->timestamps();

            $table->foreign('recruited_by_id')
                ->references('id')
                ->on('players')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_recruits');
    }
}

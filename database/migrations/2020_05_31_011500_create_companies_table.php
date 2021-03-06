<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'companies', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->primary()->comment('The companies in game ID from Torn');
                $table->string('name', 250);
                $table->unsignedBigInteger('player_id');
                $table->unsignedBigInteger('company_type')->default(0);
                $table->integer('rank')->nullable();
                $table->integer('hired_employees')->nullable();
                $table->integer('max_employees')->nullable();
                $table->boolean('isOwner')->default(false);
                $table->timestamps();

                $table->foreign('player_id')
                    ->references('id')
                    ->on('players')
                    ->onDelete('cascade');

                $table->foreign('company_type')
                    ->references('id')
                    ->on('company_types');
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
        Schema::dropIfExists('companies');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('servers');
        Schema::enableForeignKeyConstraints();

        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('nameServer')->nullable();
            $table->string('urlServer');
            $table->string('pathSource')->nullable();
            $table->string('pathLog')->nullable();
            $table->string('scriptStart');
            $table->string('scriptStop');
            $table->string('scriptTask')->nullable();
            $table->string('urlGit')->nullable();
            $table->string('lastRunTime')->nullable();
            $table->string('lastSuccessTime')->nullable();
            $table->string('lastFailTime')->nullable();
            $table->string('status')->default(0);
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('servers');
        Schema::enableForeignKeyConstraints();
    }
}

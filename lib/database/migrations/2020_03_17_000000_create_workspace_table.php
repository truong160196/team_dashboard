<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkspaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('histories');
        Schema::dropIfExists('workspaces');
        Schema::dropIfExists('access_tokens');
        Schema::enableForeignKeyConstraints();

        Schema::create('access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('key')->unique();
            $table->string('isDelete')->default(0);
            $table->string('isActive')->default(1);
            $table->timestamps();
        });

        Schema::create('workspaces', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('key')->unique();
            $table->string('path')->nullable();
            $table->unsignedBigInteger('access_id');
            $table->string('isDelete')->default(0);
            $table->foreign('access_id')->references('id')->on('access_tokens')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->string('comment')->nullable();
            $table->string('user')->nullable();
            $table->string('date')->nullable();
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('workspaces')->onDelete('cascade');

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
        Schema::dropIfExists('histories');
        Schema::dropIfExists('workspaces');
        Schema::dropIfExists('access_tokens');
        Schema::enableForeignKeyConstraints();
    }
}

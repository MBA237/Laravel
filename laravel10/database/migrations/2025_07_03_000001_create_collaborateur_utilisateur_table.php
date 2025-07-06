<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('collaborateur_utilisateur', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('tbl_collaborateur_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tbl_collaborateur_id')->references('id')->on('tbl_collaborateurs')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('collaborateur_utilisateur');
    }
};

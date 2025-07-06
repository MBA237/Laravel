<?php

use App\Models\TblProjet;
use App\Models\TblCollaborateur;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_collaborateur_projets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tbl_collaborateur_id');
            $table->unsignedBigInteger('tbl_projet_id');
            $table->timestamps();

            $table->foreign('tbl_collaborateur_id')->references('id')->on('tbl_collaborateurs')->onDelete('cascade');
            $table->foreign('tbl_projet_id')->references('id')->on('tbl_projets')->onDelete('cascade');
            $table->unique(['tbl_collaborateur_id', 'tbl_projet_id'], 'collab_projet_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collaborateur_projet');
    }
};

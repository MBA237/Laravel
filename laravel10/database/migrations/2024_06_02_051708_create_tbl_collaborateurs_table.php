<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\TblProjet;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_collaborateurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom_collab');
            $table->string('email_collab');
            // Suppression de la clé étrangère tbl_projet_id (relation many-to-many désormais)
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['nom_collab', 'email_collab']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_collaborateurs');
    }
};

<?php

use App\Models\Categorie;
use App\Models\PorteurDeProjet;
use App\Models\User;
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
        Schema::create('projets', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('image');
            $table->text('objectif');
            $table->longText('description');
            $table->string('echeance');
            $table->string('budget');
            $table->enum('etat', ['Disponible', 'Financé'])->default('Disponible');
            $table->foreignIdFor(Categorie::class)->constrained()->onDelete('cascade');
            // $table->enum('categorie', ['Agriculture', 'Education', 'Santé', 'Elevage', 'Informatique']);
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projets');
    }
};

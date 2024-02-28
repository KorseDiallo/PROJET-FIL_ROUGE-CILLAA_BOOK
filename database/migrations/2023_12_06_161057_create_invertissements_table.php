<?php

use App\Models\User;
use App\Models\Projet;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invertissements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('montant');
            $table->text('description');
            $table->enum('status', ['Accepter', 'Refuser'])->nullable();
            $table->foreignIdFor(Projet::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invertissements');
    }
};

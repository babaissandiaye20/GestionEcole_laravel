<?php

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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Champ pour le nom de l'utilisateur
            $table->string('prenom'); // Champ pour le prénom de l'utilisateur
            $table->string('adresse'); // Champ pour l'adresse
            $table->string('telephone')->unique(); // Champ pour le téléphone (unique)
            $table->enum('fonction', ['Admin', 'Apprenant', 'Manager', 'CM', 'Coach']); // Enum pour la fonction
            $table->string('email')->unique(); // Champ pour l'email (unique)
            $table->string('photo')->nullable(); // Champ pour la photo de l'utilisateur (nullable)
            $table->enum('statut', ['Actif', 'Bloquer'])->default('Actif'); // Statut : Bloquer ou Actif
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); // Champ pour le mot de passe
            $table->rememberToken(); // Token pour se souvenir de l'utilisateur
            $table->timestamps(); // Dates de création et de mise à jour
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

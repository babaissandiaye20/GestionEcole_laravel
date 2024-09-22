<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Insertion des données utilisateur directement dans DatabaseSeeder
        User::create([
            'nom' => 'Doe',
            'prenom' => 'John',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'telephone' => '0123456789',
            'adresse' => '123 Rue Principale',
            'fonction' => 'Admin',
            'statut' => 'Actif',
        ]);

        User::create([
            'nom' => 'Smith',
            'prenom' => 'Jane',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'telephone' => '0987654321',
            'adresse' => '456 Avenue Secondaire',
            'fonction' => 'Manager',
            'statut' => 'Actif',
        ]);
    }
}

<?php

namespace App\Models;
use App\Models\FireBaseModel;


class UserFireBase extends FireBaseModel
{
    // La collection Firebase pour les utilisateurs
   protected $collection = 'users';

    // Attributs pouvant être assignés en masse
    protected $fillable = [
        'nom', 'prenom', 'adresse', 'telephone', 'fonction', 'email', 'photo', 'password', 'statut',
    ];
}

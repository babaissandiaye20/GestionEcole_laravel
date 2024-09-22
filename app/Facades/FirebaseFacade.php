<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FirebaseFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'firebase_service'; // Le nom de l'alias dans le conteneur de services
    }
}

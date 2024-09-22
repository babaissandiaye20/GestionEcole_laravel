<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class UserFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'user_model'; // Le nom de l'alias dans le conteneur de services
    }
}

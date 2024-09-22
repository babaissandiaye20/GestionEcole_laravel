<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ReferentielFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ReferentielFacade'; // Le nom de l'alias dans le conteneur de services
    }
}

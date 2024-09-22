<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PromotionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'PromotionFacade'; // Le nom de l'alias dans le conteneur de services
    }
}

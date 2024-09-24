<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\UserFireBase;
use App\Models\Referentiel;
use App\Models\Promotion;
use App\Repositories\UserFirebaseRepository;
use App\Repositories\UserFirebaseRepositoryInterface;
use App\Models\FirebaseServiceBaseInterface;
use App\Services\UserFirebaseService;
use App\Services\UserFirebaseServiceInterface;
use Kreait\Firebase\Factory;
use App\Repositories\ReferentielRepositoryInterface;
use App\Repositories\ReferentielFirebaseRepository;
use App\Services\ReferentielServiceInterface;
use App\Services\ReferentielFirebaseService;
use App\Services\PromotionServiceInterface;
use App\Services\PromotionService;
use App\Repositories\PromotionRepositories;
use App\Repositories\PromotionRepositoryInterface;
class ModelsProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

         $this->app->singleton('ReferentielFacade', function ($app) {
                    return new Referentiel();
                });
                $this->app->singleton('PromotionFacade', function ($app) {
                                    return new Promotion();
                                });

                   $this->app->singleton('user_model', function ($app) {
                                    return new UserFireBase();
                                });
                 $this->app->bind(UserFirebaseRepositoryInterface::class, UserFirebaseRepository::class);
                $this->app->bind(UserFirebaseServiceInterface::class, UserFirebaseService::class);

                $this->app->singleton(\Kreait\Firebase\Auth::class, function ($app) {
                         $firebaseConfig = '/var/www/html/config/firebase_credentials.json';
                            $projectId = config('firebase.project_id');

                            $firebase = (new Factory)
                                ->withServiceAccount($firebaseConfig)
                                ->withProjectId($projectId); // Ensure project ID is provided

                            return $firebase->createAuth();
                        });
                         // Enregistrement des interfaces et des implémentations
                                $this->app->singleton(ReferentielRepositoryInterface::class, ReferentielFirebaseRepository::class);
                                $this->app->singleton(ReferentielServiceInterface::class,ReferentielFirebaseService::class);

                                  $this->app->bind(PromotionServiceInterface::class, PromotionService::class);
                                   $this->app->bind(PromotionRepositoryInterface::class, PromotionRepositories::class);


    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace App\Repositories;

use App\Facades\FirebaseFacade;
use  App\Facades\ReferentielFacade;
class ReferentielFirebaseRepository implements ReferentielRepositoryInterface
{
   protected $collection ='referentiels';
    public function create(array $data)
    {
        return ReferentielFacade::create($this->collection,$data);
    }

    public function read(string $id)
    {
        return ReferentielFacade::read($this->collection,$id);
    }

    public function update(string $id, array $data)
    {
        return ReferentielFacade::update($this->collection,$id, $data);
    }

    public function delete(string $id)
    {
        return ReferentielFacade::delete($this->collection,$id);
    }

    public function find(string $field, string $value)
    {
        return ReferentielFacade::find($this->collection,$field, $value);
    }

    public function findAll()
    {
        return ReferentielFacade::findAll($this->collection);
    }
 /*public function findAll(array $filters = [])
    {
        $referentiels = ReferentielFacade::findAll($this->collection);

        // Appliquer les filtres
        if (!empty($filters)) {
            if (isset($filters['libelle'])) {
                // Filtrer par libellé
                $referentiels = array_filter($referentiels, function ($referentiel) use ($filters) {
                    return stripos($referentiel['libelle'], $filters['libelle']) !== false;
                });
            }

            if (isset($filters['module'])) {
                // Filtrer par module
                $referentiels = array_filter($referentiels, function ($referentiel) use ($filters) {
                    foreach ($referentiel['competences'] as $competence) {
                        foreach ($competence['modules'] as $module) {
                            if (stripos($module['nom'], $filters['module']) !== false) {
                                return true;
                            }
                        }
                    }
                    return false;
                });
            }
        }

        return array_values($referentiels); // Renvoie le tableau filtré
    }*/


    public function uploadPhoto(string $filePath, string $fileName): string
    {
        return ReferentielFacade::uploadPhoto($filePath, $fileName);
    }
 public function findByLibelle(string $libelle)
 {
     return FirebaseFacade::findByLibelle($this->collection,$libelle);
 }
}

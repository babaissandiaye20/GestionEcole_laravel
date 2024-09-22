<?php

namespace App\Models;

use App\Facades\FirebaseFacade;

abstract class FireBaseModel implements FireBaseModelInterface
{




    // Créer un nouveau document dans la collection Firebase
   public function create(string $collection, array $data)
    {
        return FirebaseFacade::create($collection,$data);
    }

    // Lire un document à partir de son ID
    public function read(string $collection,string $id)
    {
        return FirebaseFacade::read($collection,$id);
    }

    // Mettre à jour un document à partir de son ID
    public function update(string $collection,string $id, array $data)
    {
        return FirebaseFacade::update($collection,$id, $data);
    }

    // Supprimer un document à partir de son ID
    public function delete( string $collection,string $id)
    {
        return FirebaseServiceFacade::delete($collection,$id);
    }

    // Trouver un document par un champ donné
    public function find(string $collection,string $field, string $value)
    {
        return FirebaseFacade::find($collection,$field, $value);
    }

    // Récupérer tous les documents
    public function findAll(string $collection)
    {
        return FirebaseFacade::findAll($collection);
    }
}

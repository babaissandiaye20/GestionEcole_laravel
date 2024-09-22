<?php

namespace App\Models;

interface FireBaseModelInterface
{
     public function create(string $collection, array $data);
     public function read(string $collection,string $id);
         public function update(string $collection,string $id, array $data);
   public function delete( string $collection,string $id);
     public function find(string $collection,string $field, string $value);  // Pour trouver un document par champ
     public function findAll(string $collection);// Pour récupérer tous les documents
    }



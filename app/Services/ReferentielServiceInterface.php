<?php

namespace App\Services;

interface ReferentielServiceInterface
{
    public function createReferentiel(array $data);
    public function getReferentielById(string $id);
    public function updateReferentiel(string $id, array $data);
    public function deleteReferentiel(string $id);
}

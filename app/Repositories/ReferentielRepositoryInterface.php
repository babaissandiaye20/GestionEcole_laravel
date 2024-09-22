<?php

namespace App\Repositories;

interface ReferentielRepositoryInterface
{
    public function create(array $data);
    public function read(string $id);
    public function update(string $id, array $data);
    public function delete(string $id);
    public function find(string $field, string $value);
    public function findAll();
    public function uploadPhoto(string $filePath, string $fileName): string;
}

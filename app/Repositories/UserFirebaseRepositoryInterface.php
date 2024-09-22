<?php

namespace App\Repositories;

interface UserFirebaseRepositoryInterface
{
    public function createUser(array $data);

    public function getUserById(string $id);

    public function updateUser(string $id, array $data);

    public function deleteUser(string $id);

    public function findUserByField(string $field, string $value);

    public function getAllUsers();
}

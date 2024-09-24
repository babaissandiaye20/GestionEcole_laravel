<?php

namespace App\Services;

interface UserFirebaseServiceInterface
{
    public function createUser(array $data,$currentRole);

    public function getUserById(string $id);

    public function updateUser(string $id, array $data);

    public function deleteUser(string $id);

    public function findUserByField(string $field, string $value);

    public function getAllUsers($role);
}

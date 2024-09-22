<?php

namespace App\Repositories;

use App\Facades\UserFacade;
use App\Facades\FirebaseFacade;
class UserFirebaseRepository implements UserFirebaseRepositoryInterface
{
       protected $collection = 'users';
    public function createUser(array $data)
    {
         return UserFacade::create($this->collection, $data);
    }

    public function getUserById(string $id)
    {
        return UserFacade::read($this->collection,$id);
    }

    public function updateUser(string $id, array $data)
    {
        return UserFacade::update($this->collection,$id, $data);
    }

    public function deleteUser(string $id)
    {
        return UserFacade::delete($this->collection,$id);
    }

    public function findUserByField(string $field, string $value)
    {
        return UserFacade::find($this->collection,$field, $value);
    }

    public function getAllUsers()
    {
        return UserFacade::findAll($this->collection);

    }
public function createUserWithEmailAndPassword($email, $password)
{
    return FirebaseFacade::createUserWithEmailAndPassword($email, $password);
}

public function uploadPhoto($filePath, $fileName)
{
    return FirebaseFacade::uploadPhoto($filePath, $fileName);
}

}

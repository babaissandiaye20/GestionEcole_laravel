<?php

namespace App\Services;

use App\Repositories\UserFirebaseRepositoryInterface;

class UserFirebaseService implements UserFirebaseServiceInterface
{
    protected $userRepository;

    public function __construct(UserFirebaseRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

   public function createUser(array $data)
   {
       // Créez l'utilisateur dans Firebase Authentication avec email et mot de passe
       $firebaseUserId = $this->userRepository->createUserWithEmailAndPassword($data['email'], $data['password']);

       // Si une photo est envoyée, uploadez-la dans Firebase Storage
       if (isset($data['photo'])) {
           $filePath = $data['photo']->getPathname();
           $fileName = time() . '_' . $data['photo']->getClientOriginalName(); // Génère un nom unique

           // Utilisez la méthode uploadPhoto pour stocker l'image dans Firebase Storage
           $photoUrl = $this->userRepository->uploadPhoto($filePath, $fileName);
           $data['photo'] = $photoUrl; // Ajoutez l'URL de la photo aux données utilisateur
       }

       // Créez l'utilisateur dans la base de données Firebase avec ses informations
       $data['firebaseUserId'] = $firebaseUserId; // Ajoutez l'ID Firebase de l'utilisateur
       return $this->userRepository->createUser($data);
   }


    public function getUserById(string $id)
    {
        return $this->userRepository->getUserById($id);
    }

    public function updateUser(string $id, array $data)
    {
        return $this->userRepository->updateUser($id, $data);
    }

    public function deleteUser(string $id)
    {
        return $this->userRepository->deleteUser($id);
    }

    public function findUserByField(string $field, string $value)
    {
        return $this->userRepository->findUserByField($field, $value);
    }

    public function getAllUsers()
    {
        return $this->userRepository->getAllUsers();
    }
}

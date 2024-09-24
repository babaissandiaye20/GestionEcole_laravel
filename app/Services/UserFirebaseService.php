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

   public function createUser(array $data, $currentRole)
   {
       $validRoles = [];

       // Vérifier les rôles autorisés pour l'utilisateur en cours
       if ($currentRole === 'Admin') {
           $validRoles = ['Admin', 'Coach', 'Manager', 'CM', 'apprenant'];
       } elseif ($currentRole === 'Manager') {
           $validRoles = ['Coach', 'Manager', 'CM', 'apprenant'];
       } else {
           throw new \Exception('Non autorisé à créer cet utilisateur.');
       }

       // Vérifier si le rôle est valide
       if (!in_array($data['role'], $validRoles)) {
           throw new \Exception('Le rôle fourni est invalide.');
       }

       // Créer l'utilisateur dans Firebase et la base de données
       $firebaseUserId = $this->userRepository->createUserWithEmailAndPassword($data['email'], $data['password']);
       $data['firebaseUserId'] = $firebaseUserId;

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

    public function getAllUsers($role)
    {
        return $this->userRepository->getAllUsers($role);
    }
}

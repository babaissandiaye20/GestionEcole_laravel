<?php
namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Storage;
use Illuminate\Support\Facades\Log;

class FirebaseServiceBase implements FirebaseServiceBaseInterface
{
    protected $database;
    protected $auth;
    protected $storage;

    public function __construct()
    {
        $firebase = (new Factory)
            ->withServiceAccount('/home/babaissa/Bureau/GestionEcoleBis3/config/firebase_credentials.json')
            ->withDatabaseUri('https://gesecole-1137a-default-rtdb.firebaseio.com/');

        $this->database = $firebase->createDatabase();
        $this->auth = $firebase->createAuth();
        $this->storage = $firebase->createStorage();
    }

    // Méthode pour créer un utilisateur avec email et mot de passe
    public function createUserWithEmailAndPassword($email, $password)
    {
        if (empty($email) || empty($password)) {
            Log::error('Email or password is missing.');
            throw new \InvalidArgumentException('Email and password must be provided.');
        }

        try {
            $user = $this->auth->createUserWithEmailAndPassword($email, $password);
            return $user->uid;
        } catch (\Exception $e) {
            Log::error('Firebase user creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Error while creating the user in Firebase'], 500);
        }
    }


    // Méthode pour uploader une photo dans Firebase Storage
    public function uploadPhoto($filePath, $fileName)
    {
        try {
            $bucket = $this->storage->getBucket();
            $storagePath = 'photos/' . $fileName;

            $bucket->upload(fopen($filePath, 'r'), [
                'name' => $storagePath
            ]);

            // Générer une URL de téléchargement publique
            $object = $bucket->object($storagePath);
            $object->update(['acl' => []], ['predefinedAcl' => 'PUBLICREAD']);

            return $object->signedUrl(new \DateTime('+1 hour')); // URL valide pour 1 heure
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'upload de la photo dans Firebase : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'upload de la photo dans Firebase'], 500);
        }
    }

    // Méthodes CRUD pour la base de données Firebase
   public function create(string $collection, array $data)
       {
           $reference = $this->database->getReference($collection);
           return $reference->push($data);
       }


    public function read(string $collection, string $id)
       {
           $reference = $this->database->getReference($collection . '/' . $id);
           $snapshot = $reference->getSnapshot();
           return $snapshot->exists() ? $snapshot->getValue() : null;
       }

 public function update(string $collection, string $id, array $data)
     {
         $reference = $this->database->getReference($collection . '/' . $id);
         return $reference->update($data);
     }


   public function delete(string $collection, string $id)
       {
           $reference = $this->database->getReference($collection . '/' . $id);
           return $reference->remove();
       }

    public function find(string $collection, string $field, string $value)
        {
            $reference = $this->database->getReference($collection);
            $query = $reference->orderByChild($field)->equalTo($value)->getSnapshot();

            if (!$query->exists()) {
                return null;
            }

            $results = [];
            foreach ($query->getValue() as $key => $data) {
                $results[$key] = $data;
            }

            return $results;
        }

   public function findAll(string $collection)
       {
           $reference = $this->database->getReference($collection);
           $snapshot = $reference->getSnapshot();

           return $snapshot->exists() ? $snapshot->getValue() : [];
       }
public function findByLibelle( string $collection,string $libelle)
{
    return $this->database->getReference($collection)
                          ->orderByChild('libelle')
                          ->equalTo($libelle)
                          ->getValue();
}
public function readbis($collection, $userId)
{
    // Assuming you're using Firebase SDK to fetch data
    $userSnapshot = $firebase->getDatabase()
                             ->getReference("$collection/$userId")
                             ->getSnapshot();

    if ($userSnapshot->exists()) {
        $userData = $userSnapshot->getValue();
        // Add the Firebase key as 'id' in the user data
        $userData['id'] = $userSnapshot->getKey();
        return $userData;
    }

    return null;
}
}

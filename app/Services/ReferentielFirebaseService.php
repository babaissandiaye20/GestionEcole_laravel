<?php

namespace App\Services;

use App\Repositories\ReferentielRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Facades\UserFacade;
class ReferentielFirebaseService implements ReferentielServiceInterface
{
    protected $referentielRepository;


    public function __construct(ReferentielRepositoryInterface $referentielRepository)
    {
        $this->referentielRepository = $referentielRepository;
    }

  public function createReferentiel(array $data)
  {
      // Vérifier si le libellé existe déjà
      $existingReferentiel = $this->referentielRepository->findByLibelle($data['libelle']);

      if ($existingReferentiel) {
          return [
              'success' => false,
              'error' => 'Un référentiel avec ce libellé existe déjà'
          ];
      }

      $randomId = random_int(100000, 999999);

      // Upload de la photo (si présente)
      $photoUrl = null;
      if (!empty($data['photo'])) {
          try {
              $photoUrl = $this->referentielRepository->uploadPhoto($data['photo'], 'referentiel_' . $randomId);
          } catch (Exception $e) {
              Log::error('Erreur lors de l\'upload de la photo : ' . $e->getMessage());
              throw new \RuntimeException('Erreur lors de l\'upload de la photo');
          }
      }

      // Récupérer l'utilisateur à partir de l'ID (s'il est fourni)
      $userId = $data['user_id'] ?? null;
      $user = null;

      if ($userId) {
          try {
              $user = UserFacade::read('users', $userId);

              if (!$user) {
                  return [
                      'success' => false,
                      'error' => "L'utilisateur avec l'ID $userId n'existe pas."
                  ];
              }

              // Vérifier si l'utilisateur a le rôle "Apprenant"
              if ($user['role'] !== 'apprenant') {
                  return [
                      'success' => false,
                      'error' => "L'utilisateur doit avoir le rôle 'Apprenant' pour créer un référentiel."
                  ];
              }

          } catch (Exception $e) {
              Log::error('Erreur lors de la récupération de l\'utilisateur : ' . $e->getMessage());
              return [
                  'success' => false,
                  'error' => 'Erreur lors de la récupération de l\'utilisateur.'
              ];
          }
      } else {
          return [
              'success' => false,
              'error' => "L'ID de l'utilisateur est requis."
          ];
      }

      // Préparer les données du référentiel
      $referentielData = [
          'id' => $randomId,
          'code' => $data['code'],
          'libelle' => $data['libelle'],
          'description' => $data['description'],
          'statut' => $data['statut'] ?? 'Actif',
          'photo' => $photoUrl,
          'competences' => $this->formatCompetences($data['competences'] ?? []),
          'user_id' =>$user  // Associer l'utilisateur trouvé avec l'ID
      ];

      // Créer le référentiel
      return $this->referentielRepository->create($referentielData);
  }


    public function getReferentielById(string $id)
    {
        return $this->referentielRepository->read($id);
    }

    public function updateReferentiel(string $id, array $data)
    {
     dd($request->all());
        // Gestion du statut uniquement si présent dans les données
        if (isset($data['statut'])) {
            $data['statut'] = $data['statut'] === 'Actif' ? 'Actif' : 'Inactif';
        } else {
            unset($data['statut']); // Retirer le champ si non fourni
        }

        // Mise à jour du référentiel avec les données fournies
        try {

            return $this->referentielRepository->update($id, $data);
        } catch (Exception $e) {
            Log::error('Erreur lors de la mise à jour du référentiel avec ID ' . $id . ' : ' . $e->getMessage());
            throw new \RuntimeException('Erreur lors de la mise à jour du référentiel');
        }
    }


    public function deleteReferentiel(string $id)
    {
        return $this->referentielRepository->delete($id);
    }

    public function getAllReferentiels()
    {
        return $this->referentielRepository->findAll(); // Supposons que readAll() existe dans le repository
    }
/*public function getFilteredReferentiels(array $filters = [])
{
    return $this->referentielRepository->findAll($filters);
}*/
    protected function formatCompetences(array $competences)
    {
        return array_map(function ($competence) {
            return [
                'nom' => $competence['nom'],
                'description' => $competence['description'],
                'duree_acquisition' => $competence['duree_acquisition'],
                'type' => $competence['type'],
                'modules' => $this->formatModules($competence['modules'] ?? [])
            ];
        }, $competences);
    }


    protected function formatModules(array $modules)
    {
        return array_map(function ($module) {
            return [
                'nom' => $module['nom'],
                'description' => $module['description'],
                'duree_acquisition' => $module['duree_acquisition'],
            ];
        }, $modules);
    }
public function addUserToReferentiel(int $referentielId, int $userId)
{
    // Vérifier si le référentiel existe
    $referentiel = $this->referentielRepository->findById($referentielId);

    if (!$referentiel) {
        return [
            'success' => false,
            'error' => "Le référentiel avec l'ID $referentielId n'existe pas."
        ];
    }

    // Récupérer l'utilisateur par ID
    try {
        $user = UserFacade::read('users', $userId);

        if (!$user) {
            return [
                'success' => false,
                'error' => "L'utilisateur avec l'ID $userId n'existe pas."
            ];
        }

        // Vérifier si l'utilisateur a le rôle "Apprenant"
        if ($user->role !== 'Apprenant') {
            return [
                'success' => false,
                'error' => "L'utilisateur doit avoir le rôle 'Apprenant' pour être ajouté à un référentiel."
            ];
        }

    } catch (Exception $e) {
        Log::error('Erreur lors de la récupération de l\'utilisateur : ' . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Erreur lors de la récupération de l\'utilisateur.'
        ];
    }

    // Associer l'utilisateur au référentiel
    $success = $this->referentielRepository->addUserToReferentiel($referentielId, $userId);

    if (!$success) {
        return [
            'success' => false,
            'error' => 'Erreur lors de l\'association de l\'utilisateur au référentiel.'
        ];
    }

    return [
        'success' => true,
        'message' => "L'utilisateur a été ajouté avec succès au référentiel."
    ];
}



}

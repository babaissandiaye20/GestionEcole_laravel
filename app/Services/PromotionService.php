<?php
namespace App\Services;

use App\Repositories\PromotionRepositoryInterface;
use Exception;
use Carbon\Carbon;
use App\Facades\ReferentielFacade;
class PromotionService implements PromotionServiceInterface
{
    protected $promotionRepository;

    public function __construct(PromotionRepositoryInterface $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }


    public function createPromotion(array $data)
    {
        // Vérifiez si le libellé existe déjà
        if ($this->checkIfLibelleExists($data['libelle'])) {
            return response()->json(['error' => 'Le libellé de la promotion existe déjà.'], 400);
        }

        // Calcul de la date de fin si nécessaire
        if (!isset($data['date_fin']) && isset($data['duree'])) {
            $data['date_fin'] = \Carbon\Carbon::parse($data['date_debut'])->addMonths($data['duree'])->format('Y-m-d');
        } elseif (!isset($data['duree']) && isset($data['date_fin'])) {
            $data['duree'] = \Carbon\Carbon::parse($data['date_debut'])->diffInMonths($data['date_fin']);
        }

        // Par défaut, l'état est "Inactif"
        $data['etat'] = $data['etat'] ?? 'Inactif';

        try {
            // Désactiver les autres promotions si celle-ci est activée
            if ($data['etat'] === 'Actif') {
                $this->deactivateOtherPromotions();
            }

            // Assurez-vous que le référentiel existe déjà en vérifiant son ID
            if (isset($data['referentiel_id'])) {
                $referentiel = $this->verifyReferentielExists($data['referentiel_id']);

                if (!$referentiel) {
                    return response()->json(['error' => 'Le référentiel spécifié n\'existe pas.'], 404);
                }
            } else {
                return response()->json(['error' => 'L\'ID du référentiel est requis.'], 400);
            }

            // Stockez l'objet référentiel complet dans les données de promotion
            $promotionData = [
                'libelle' => $data['libelle'],
                'referentiel' => $referentiel, // Stockez l'objet complet au lieu de l'ID
                'date_debut' => $data['date_debut'],
                'date_fin' => $data['date_fin'],
                'duree' => $data['duree'],
                'etat' => $data['etat'],
                'photo' => $data['photo'] ?? null,

            ];

            // Créer la promotion dans la base de données
            $this->promotionRepository->create($promotionData);

            return response()->json(['message' => 'Promotion créée avec succès.'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

  protected function verifyReferentielExists($referentielId)
  {

      // Appel de la méthode find avec trois arguments
     return ReferentielFacade::read('referentiels',$referentielId);
  }

   /*protected function deactivateOtherPromotions()
   {
       $this->promotionRepository->deactivateOtherPromotions();
   }*/

    protected function checkIfLibelleExists(string $libelle): bool
    {
        $promotions = $this->promotionRepository->getAllPromotions();
        foreach ($promotions as $promotion) {
            if (isset($promotion['libelle']) && $promotion['libelle'] === $libelle) {
                return true;
            }
        }
        return false;
    }

    protected function deactivateOtherPromotions()
    {
        $this->promotionRepository->deactivateOtherPromotions();
    }

    protected function formatReferentiels(array $referentiels)
    {
        $formattedReferentiels = [];
        foreach ($referentiels as $referentiel) {
            $referentielData = [
                'id' => random_int(100000, 999999),
                'code' => $referentiel['code'],
                'libelle' => $referentiel['libelle'],
                'description' => $referentiel['description'] ?? null,
                'photo' => $referentiel['photo'] ?? null,
                'statut' => 'Actif',
                'competences' => $this->formatCompetences($referentiel['competences'] ?? []),
                'apprenants' => $this->formatApprenants($referentiel['apprenants'] ?? [])
            ];
            $formattedReferentiels[] = $referentielData;
        }
        return $formattedReferentiels;
    }

    protected function formatCompetences(array $competences)
    {
        $formattedCompetences = [];
        foreach ($competences as $competence) {
            $competenceData = [
                'nom' => $competence['nom'],
                'description' => $competence['description'] ?? null,
                'duree_acquisition' => $competence['duree_acquisition'] ?? null,
                'type' => $competence['type'] ?? null,
                'modules' => $this->formatModules($competence['modules'] ?? [])
            ];
            $formattedCompetences[] = $competenceData;
        }
        return $formattedCompetences;
    }

    protected function formatModules(array $modules)
    {
        $formattedModules = [];
        foreach ($modules as $module) {
            $moduleData = [
                'nom' => $module['nom'],
                'description' => $module['description'] ?? null,
                'duree_acquisition' => $module['duree_acquisition'] ?? null,
            ];
            $formattedModules[] = $moduleData;
        }
        return $formattedModules;
    }

    protected function formatApprenants(array $apprenants)
    {
        $formattedApprenants = [];
        foreach ($apprenants as $apprenant) {
            $apprenantData = [
                'nom' => $apprenant['nom'],
                'prenom' => $apprenant['prenom'],
                'adresse' => $apprenant['adresse'],
                'email' => $apprenant['email'],
                'password' => bcrypt($apprenant['password']),
                'telephone' => $apprenant['telephone'],
                'role' => $apprenant['role'],
                'statut' => $apprenant['statut'] ?? 'Actif',
                'photo' => $apprenant['photo'] ?? null,
                'referentiel' => $apprenant['referentiel'] ?? null,
            ];
            $formattedApprenants[] = $apprenantData;
        }
        return $formattedApprenants;
    }

  public function getActivePromotion()
  {
      try {
          $promotion = $this->promotionRepository->getActifPromotion();
          if ($promotion) {
              if (isset($promotion['referentiels'])) {
                  $promotion['referentiels'] = $this->formatReferentiels($promotion['referentiels']);
              } else {
                  $promotion['referentiels'] = []; // ou une autre valeur par défaut
              }
              return response()->json($promotion, 200);
          } else {
              return response()->json(['message' => 'Aucune promotion actuelle.'], 404);
          }
      } catch (Exception $e) {
          return response()->json(['error' => $e->getMessage()], 500);
      }
  }

public function findPromotionbyid(string $field, string $value){
 return $this->promotionRepository->findPromotionbyid($field, $value);
 }
      public function deletePromotion(string $id){
      return  $this->promotionRepository->delete($id);
      }
  public function updatePromotionEtat(string $id, string $etat)
  {
      try {
          // Si la nouvelle promotion est activée, désactiver les autres promotions
          if ($etat === 'Actif') {
              $this->deactivateOtherPromotions();
          }

          // Mise à jour de l'état de la promotion
          $this->promotionRepository->updatePromotionEtat($id, $etat);

          return response()->json(['message' => 'L\'état de la promotion a été mis à jour avec succès.'], 200);
      } catch (Exception $e) {
          return response()->json(['error' => $e->getMessage()], 500);
      }
  }

public function updatePromotion(string $id, array $data)
{
    try {
        // Ensure the 'libelle' key exists and is valid
        if (!isset($data['libelle'])) {
            return response()->json(['error' => 'Le libellé est requis.'], 400);
        }

        // Check if another promotion with the same 'libelle' already exists, excluding the current one
        if ($this->checkIfLibelleExists($data['libelle'], $id)) {
            return response()->json(['error' => 'Le libellé de la promotion existe déjà.'], 400);
        }

        // Check if the 'referentiel_id' exists and validate it
        if (isset($data['referentiel_id'])) {
            $referentiel = $this->verifyReferentielExists($data['referentiel_id']);

            if (!$referentiel) {
                return response()->json(['error' => 'Le référentiel spécifié n\'existe pas.'], 404);
            }

            // Add the referentiel object to the data
            $data['referentiel'] = $referentiel;
        }

        // Calculate 'date_fin' if necessary
        if (!isset($data['date_fin']) && isset($data['duree'])) {
            $data['date_fin'] = \Carbon\Carbon::parse($data['date_debut'])->addMonths($data['duree'])->format('Y-m-d');
        } elseif (!isset($data['duree']) && isset($data['date_fin'])) {
            $data['duree'] = \Carbon\Carbon::parse($data['date_debut'])->diffInMonths($data['date_fin']);
        }

        // If the promotion is being activated, deactivate others
        if (isset($data['etat']) && $data['etat'] === 'Actif') {
            $this->deactivateOtherPromotions();
        }

        // Update the promotion with the new data
        $this->promotionRepository->updatePromotion($id, $data);

        return response()->json(['message' => 'Promotion mise à jour avec succès.'], 200);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

}

<?php

namespace App\Http\Controllers;

use App\Services\ReferentielServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReferentielController extends Controller
{
    protected $referentielService;

    public function __construct(ReferentielServiceInterface $referentielService)
    {
        $this->referentielService = $referentielService;
    }

    // Méthode pour créer un référentiel
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:255',
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:1024', // Exemple de validation d'une image
            'competences' => 'nullable|array',
             'user_id'=>'nullable|string',
        ]);

        try {
        //dd($validatedData);
            $referentiel = $this->referentielService->createReferentiel($validatedData);
            return response()->json($referentiel, 201); // Retourne le référentiel créé
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du référentiel : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la création du référentiel'], 500);
        }
    }

    // Méthode pour obtenir un référentiel par ID
    public function show($id)
    {
        try {
            $referentiel = $this->referentielService->getReferentielById($id);
            if ($referentiel) {
                return response()->json($referentiel);
            } else {
                return response()->json(['error' => 'Référentiel non trouvé'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du référentiel : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération du référentiel'], 500);
        }
    }
/*public function index(){
try {
            $referentiel = $this->referentielService-> getAllReferentiels();

                return response()->json($referentiel);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du référentiel : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération du référentiel'], 500);
        }
    }*/
    public function index(Request $request)
    {
        $filters = $request->only(['competence', 'module']); // Filtres par compétence et module

        try {
            $referentiels = $this->referentielService->getFilteredReferentiels($filters);
            return response()->json($referentiels);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des référentiels : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération des référentiels'], 500);
        }
    }

/*public function index(Request $request)
{
    $filters = $request->only(['libelle', 'module']);

    try {
        $referentiels = $this->referentielService->getFilteredReferentiels($filters);
        return response()->json($referentiels);
    } catch (\Exception $e) {
        Log::error('Erreur lors de la récupération des référentiels : ' . $e->getMessage());
        return response()->json(['error' => 'Erreur lors de la récupération des référentiels'], 500);
    }
}*/

    // Méthode pour mettre à jour un référentiel

    // Méthode pour supprimer un référentiel
    public function destroy($id)
    {
        try {
            $this->referentielService->deleteReferentiel($id);
            return response()->json(['message' => 'Référentiel supprimé avec succès']);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du référentiel : ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la suppression du référentiel'], 500);
        }
    }
public function addUsersToReferentiel(Request $request, $referentielId)
{
    $validatedData = $request->validate([
        'users' => 'required|array',
        'users.*.id' => 'required|string', // ID des utilisateurs
    ]);

    try {
        // Appel au service pour ajouter les utilisateurs au référentiel
        $result = $this->referentielService->addUsersToReferentiel($referentielId, $validatedData['users']);

        // Vérification du résultat
        if ($result['success']) {
            return response()->json(['message' => 'Tous les utilisateurs ont été ajoutés avec succès'], 200);
        } else {
            return response()->json(['error' => 'Erreur lors de l\'ajout des utilisateurs', 'invalid_users' => $result['invalid_users']], 400);
        }
    } catch (\Exception $e) {
        Log::error('Erreur lors de l\'ajout des utilisateurs au référentiel : ' . $e->getMessage());
        return response()->json(['error' => 'Erreur serveur'], 500);
    }
}
public function update(Request $request, $id)
{
    $validatedData = $request->validate([
        'code' => 'nullable|string|max:255',
        'libelle' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'photo' => 'nullable|image|max:1024',
        'competences' => 'nullable|array',
        'competences.*.id' => 'sometimes|exists:competences,id', // Validation pour les ID existants des compétences
        'competences.*.nom' => 'required|string|max:255',  // Validation pour chaque compétence
        'competences.*.description' => 'nullable|string',
        'competences.*.duree_acquisition' => 'nullable|integer',
        'competences.*.type' => 'nullable|string',
        'user_id' => 'nullable|string',
    ]);

    try {
        $referentiel = $this->referentielService->updateReferentiel($id, $validatedData);
        return response()->json($referentiel);
          Log::info('success');
    } catch (\Exception $e) {
        Log::error('Erreur lors de la mise à jour du référentiel : ' . $e->getMessage());
        return response()->json(['error' => 'Erreur lors de la mise à jour du référentiel'], 500);
    }
}
public function addUserToReferentiel(Request $request,  string $referentielId)
{
    // Valider les paramètres de la requête
    $validated = $request->validate([
        'user_id' => 'required|string',
    ]);

    $userId = $validated['user_id'];

    // Appeler le service pour ajouter l'utilisateur au référentiel
    $result = $this->referentielService->addUserToReferentiel($referentielId, $userId);

    // Vérifier le résultat et retourner une réponse appropriée
    if (!$result['success']) {
        return response()->json([
            'success' => false,
            'message' => $result['error']
        ], 400); // Code 400 pour les erreurs
    }

    return response()->json([
        'success' => true,
        'message' => $result['message']
    ], 200); // Code 200 pour succès
}



}

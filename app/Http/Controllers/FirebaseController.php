<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Facades\FirebaseFacade; // Façade pour FirebaseServiceBase

class FirebaseController extends Controller
{
    /**
     * Créer une nouvelle entrée dans Firebase.
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $result = FirebaseFacade::create($data);

        return response()->json([
            'message' => 'Document created successfully!',
            'data' => $result
        ], 201);
    }

    /**
     * Lire une entrée à partir de Firebase par ID.
     */
    public function read($id)
    {
        $result =FirebaseFacade::read($id);

        if ($result) {
            return response()->json($result, 200);
        } else {
            return response()->json(['message' => 'Document not found'], 404);
        }
    }

    /**
     * Mettre à jour une entrée existante dans Firebase.
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $result =FirebaseFacade::update($id, $data);

        return response()->json([
            'message' => 'Document updated successfully!',
            'data' => $result
        ], 200);
    }

    /**
     * Supprimer une entrée de Firebase.
     */
    public function delete($id)
    {
        FirebaseService::delete($id);

        return response()->json(['message' => 'Document deleted successfully!'], 200);
    }

    /**
     * Trouver une entrée par champ et valeur.
     */
    public function find(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');
        $result = FirebaseService::find($field, $value);

        if ($result) {
            return response()->json($result, 200);
        } else {
            return response()->json(['message' => 'No documents found'], 404);
        }
    }

    /**
     * Récupérer toutes les entrées.
     */
    public function findAll()
    {
        $result = FirebaseFacade::findAll();

        return response()->json($result, 200);
    }
}

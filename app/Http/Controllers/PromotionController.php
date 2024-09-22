<?php
namespace App\Http\Controllers;

use App\Services\PromotionServiceInterface;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    protected $promotionService;

    public function __construct(PromotionServiceInterface $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $response = $this->promotionService->createPromotion($data);
        return $response;
    }

   public function updateEtat($id, Request $request)
   {
       $etat = $request->input('etat');  // Ensure you're getting 'etat' from the request body
       return $this->promotionService->updatePromotionEtat($id, $etat);
   }

    public function getActive()
    {
        $response = $this->promotionService->getActivePromotion();
        return $response;
    }

    public function findById($id)
    {
        $response = $this->promotionService->findPromotionbyid('id', $id);
        return response()->json($response);
    }

    public function delete($id)
    {
        $response = $this->promotionService->deletePromotion($id);
        return response()->json($response);
    }
public function updatePromotion($id, Request $request)
    {
        // Récupérer toutes les données de la requête
        $data = $request->all();

        // Appeler le service pour la mise à jour
        $response = $this->promotionService->updatePromotion($id, $data);

        return response()->json($response);
    }
}

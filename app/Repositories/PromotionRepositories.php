<?php
namespace App\Repositories;

use App\Models\Promotion;
use App\Facades\PromotionFacade;
use App\Facades\FirebaseFacade;
class PromotionRepositories implements PromotionRepositoryInterface
{
    protected $collection='Promotion';

    public function __construct()
    {

    }

    public function create(array $data)
    {
        return  PromotionFacade::create($this->collection, $data);
    }

    public function getAllPromotions()
    {
        return PromotionFacade::findAll($this->collection);
    }

    public function getPromotionByLibelle(string $libelle)
    {
        return  FirebaseFacade::findByLibelle($this->collection, $libelle);
    }

    public function deactivateOtherPromotions()
    {
        $promotions = $this->getAllPromotions();
        foreach ($promotions as $id => $promotion) {
            if ($promotion['etat'] === 'Actif') {
                PromotionFacade::update($this->collection, $id, ['etat' => 'Inactif']);
            }
        }
    }

public function getActifPromotion()
{
    $promotions = $this->getAllPromotions();
    foreach ($promotions as $promotion) {
        if ($promotion['etat'] === 'Actif') {
            if (!isset($promotion['referentiels'])) {
                $promotion['referentiels'] = []; // ou une autre valeur par défaut
            }
            return $promotion;
        }
    }
    return null;
}
 public function findPromotionbyid(string $field, string $value){
 return PromotionFacade::find($this->collection,$field, $value);
 }
      public function deletePromotion(string $id){
      return PromotionFacade::delete($this->collection,$id);
      }
  public function updatePromotionEtat(string $id, string $etat)
      {
          return PromotionFacade::update($this->collection, $id, ['etat' => $etat]);
      }
public function updatePromotion(string $id, array $data)
{
    // Cherche l'ancienne promotion
    $promotion = PromotionFacade::read($this->collection,$id);

    if (!$promotion) {
        throw new Exception('La promotion n\'existe pas.');
    }

    // Met à jour les données si la promotion existe
    return PromotionFacade::update($this->collection, $id, $data);
}

}

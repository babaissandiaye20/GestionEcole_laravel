<?php

namespace App\Repositories;

interface PromotionRepositoryInterface
{
    /**
     * Crée une nouvelle promotion avec les données fournies.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Récupère toutes les promotions.
     *
     * @return mixed
     */
    public function getAllPromotions();

    /**
     * Récupère une promotion par son libellé.
     *
     * @param string $libelle
     * @return mixed
     */
    public function getPromotionByLibelle(string $libelle);

    /**
     * Désactive toutes les promotions actives.
     *
     * @return void
     */
    public function deactivateOtherPromotions();

    /**
     * Récupère la promotion qui est actuellement active.
     *
     * @return mixed|null
     */
    public function getActifPromotion();

      public function findPromotionbyid(string $field, string $value);
      public function deletePromotion(string $id);
       public function updatePromotionEtat(string $id, string $etat);

}

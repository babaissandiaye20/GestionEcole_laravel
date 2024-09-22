<?php
namespace App\Services;

interface PromotionServiceInterface
{
    public function createPromotion(array $data);
   public function updatePromotionEtat(string $id, string $etat);
    public function getActivePromotion();
    public function findPromotionbyid(string $field, string $value);
    public function deletePromotion(string $id);
}

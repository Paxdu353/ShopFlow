<?php

namespace App\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ShopApiController
{
    #[Route('/produits', name: 'api_get_produits', methods: ['GET'])]
    public function products(JsonShopStorage $storage): JsonResponse
    {
        return new JsonResponse($storage->products());
    }

    #[Route('/produit/{produit_id}', name: 'api_get_produit', requirements: ['produit_id' => '\d+'], methods: ['GET'])]
    public function product(int $produit_id, JsonShopStorage $storage): JsonResponse
    {
        $product = $storage->product($produit_id);

        return $product === null
            ? new JsonResponse(['error' => 'Produit introuvable.'], 404)
            : new JsonResponse($product);
    }

    #[Route('/commande', name: 'api_create_commande', methods: ['POST'])]
    public function order(Request $request, JsonShopStorage $storage): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return new JsonResponse(['error' => 'Le corps de la requête doit être un JSON valide.'], 400);
        }
        if (!is_array($data) || !isset($data['produit_id'], $data['quantite'])
            || !is_int($data['produit_id']) || $data['produit_id'] < 1
            || !is_int($data['quantite']) || $data['quantite'] < 1) {
            return new JsonResponse(['error' => 'produit_id et quantite sont obligatoires et doivent être des entiers positifs.'], 400);
        }
        try {
            return new JsonResponse($storage->order($data['produit_id'], $data['quantite']), 201);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 400);
        }
    }
}

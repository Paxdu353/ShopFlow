<?php

namespace App\Commande\UI\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CommandeController
{
    #[Route('/commandes', name: 'commande_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = $request->toArray();

        $panier = $data['panier'] ?? [];

        if (empty($panier)) {
            return new JsonResponse(
                ['error' => 'Panier vide'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse([
            'message' => 'Commande reçue',
            'panier' => $panier,
        ]);
    }
}

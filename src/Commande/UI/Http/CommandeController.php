<?php

namespace App\Commande\UI\Http;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommandeController
{
    #[Route('/commandes', name: 'commande_create', methods: ['POST'])]
    public function create(): Response
    {
        return new Response('Commande créée');
    }
}
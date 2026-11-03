<?php

namespace App\Catalogue;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CatalogueController extends AbstractController
{
    #[Route('/catalogue', name: 'app_catalogue')]
    public function index(): Response
    {
        return $this->render('catalogue/index.html.twig', [
            'controller_name' => 'CatalogueController',
            'produits' => [
                ['nom' => 'Kit robotique premium', 'prix' => 100, 'stock' => 10],
                ['nom' => 'Kit robotique standard', 'prix' => 50, 'stock' => 20],
                ['nom' => 'Kit robotique clochard', 'prix' => 15, 'stock' => 15],
            ]
        ]);
    }
}

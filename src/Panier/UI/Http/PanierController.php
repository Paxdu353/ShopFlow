<?php

namespace App\Panier\UI\Http;

use App\Panier\Domain\Panier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PanierController extends AbstractController
{
    #[Route('/panier', name: 'panier_index', methods: ['GET'])]
    public function index(): Response
    {
        $panier = $this->getPanier();

        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
        ]);
    }

    private function getPanier(): Panier
    {
        return new Panier('PANIER-TEST');
    }
}
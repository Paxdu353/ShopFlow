<?php

namespace App\Catalogue;

use App\Api\JsonShopStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CatalogueController extends AbstractController
{
    #[Route('/catalogue', name: 'app_catalogue')]
    public function index(JsonShopStorage $storage): Response
    {
        return $this->render('catalogue/index.html.twig', [
            'controller_name' => 'CatalogueController',
            'produits' => array_map(static fn (array $product): array => [
                'id' => $product['id'],
                'nom' => $product['name'],
                'prix' => $product['price'],
                'stock' => $product['stock'],
            ], $storage->products()),
        ]);
    }
}

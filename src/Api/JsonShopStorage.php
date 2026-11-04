<?php

namespace App\Api;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class JsonShopStorage
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/data/produits.json')]
        private readonly string $productsFile,
        #[Autowire('%kernel.project_dir%/var/shopflow.json')]
        private readonly string $stateFile,
    ) {
    }

    public function products(): array
    {
        return $this->readState()['produits'];
    }

    public function product(int $id): ?array
    {
        foreach ($this->products() as $product) {
            if ($product['id'] === $id) {
                return $product;
            }
        }

        return null;
    }

    public function order(int $productId, int $quantity): array
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('La quantité doit être un entier supérieur ou égal à 1.');
        }
        // A separate lock remains stable when the JSON file is atomically replaced.
        $lock = fopen($this->stateFile.'.lock', 'c');
        if ($lock === false || !flock($lock, LOCK_EX)) {
            throw new \RuntimeException('Impossible de verrouiller le stockage JSON.');
        }
        $temporary = null;
        try {
            $state = $this->readState();
            $index = array_search($productId, array_column($state['produits'], 'id'), true);
            if ($index === false) {
                throw new \InvalidArgumentException('Produit introuvable.');
            }
            $product = $state['produits'][$index];
            if ($product['stock'] < $quantity) {
                throw new \InvalidArgumentException('Stock insuffisant.');
            }
            $unitCents = (int) round($product['price'] * 100);
            $order = [
                'id' => count($state['commandes']) + 1,
                'produit_id' => $productId,
                'quantite' => $quantity,
                'prix_unitaire' => $unitCents / 100,
                'total' => ($unitCents * $quantity) / 100,
                'created_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
            ];
            $state['produits'][$index]['stock'] -= $quantity;
            $state['commandes'][] = $order;
            $json = json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)."\n";
            $temporary = tempnam(dirname($this->stateFile), '.shopflow-');
            if ($temporary === false || file_put_contents($temporary, $json) !== strlen($json) || !rename($temporary, $this->stateFile)) {
                throw new \RuntimeException('Impossible d’enregistrer la commande.');
            }

            return $order;
        } finally {
            if ($temporary && is_file($temporary)) {
                unlink($temporary);
            }
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function readState(): array
    {
        if (is_file($this->stateFile)) {
            return json_decode(file_get_contents($this->stateFile), true, 512, JSON_THROW_ON_ERROR);
        }

        return [
            'produits' => json_decode(file_get_contents($this->productsFile), true, 512, JSON_THROW_ON_ERROR),
            'commandes' => [],
        ];
    }
}

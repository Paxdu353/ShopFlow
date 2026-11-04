<?php

namespace App\Panier\Domain;

class Panier
{
    private string $id;
    private array $lignes;

    public function __construct(string $id, array $lignes = [])
    {
        $this->id = $id;
        $this->lignes = $lignes;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLignes(): array
    {
        return $this->lignes;
    }

    public function ajouterLigne(LignePanier $ligne): void
    {
        $this->lignes[] = $ligne;
    }

    
    public function estVide(): bool
    {
    return count($this->lignes) === 0;
    }
    
    public function getTotal(): float
    {
    $total = 0;

    foreach ($this->lignes as $ligne) {
        $total += $ligne->getPrixUnitaire() * $ligne->getQuantite();
    }

    return $total;

    
}
}
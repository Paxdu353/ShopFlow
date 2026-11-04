<?php

namespace App\Panier\Domain;

class LignePanier
{
    private string $produitId;
    private string $nom;
    private float $prixUnitaire;
    private int $quantite;

public function __construct(
    string $produitId,
    string $nom,
    float $prixUnitaire,
    int $quantite
) {
    $this->produitId = $produitId;
    $this->nom = $nom;
    $this->prixUnitaire = $prixUnitaire;
    $this->quantite = $quantite;
}

    public function getProduitId(): string
    {
        return $this->produitId;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrixUnitaire(): float
    {
        return $this->prixUnitaire;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }
}   
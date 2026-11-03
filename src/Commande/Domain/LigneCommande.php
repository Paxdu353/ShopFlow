<?php

namespace App\Commande\Domain;

class LigneCommande
{
    private string $id;
    private string $nom;
    private int $quantite;
    private Montant $prixUnitaire;

    public function __construct(
        string $id,
        string $nom,
        int $quantite,
        Montant $prixUnitaire
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->quantite = $quantite;
        $this->prixUnitaire = $prixUnitaire;
    }
}
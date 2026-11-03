<?php

namespace App\Commande\Domain;


class Montant
{
    private float $montant;

    public function __construct(float $montant)
    {
        $this->montant = $montant;
    }

    public function getMontant(): float
    {
        return $this->montant;
    }
}
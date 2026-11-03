<?php

namespace App\Commande\Domain;



class Commande {
    private CommandeId $id;
    private Montant $montant;

    public function __construct(CommandeId $id, Montant $montant) {

        $this->id = $id;
        $this->montant = $montant;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getMontant()
    {
        return $this->montant;
    }
}
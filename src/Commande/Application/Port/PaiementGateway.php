<?php

namespace App\Commande\Application\Port;

use App\Commande\Domain\CommandeId;
use App\Commande\Domain\Montant;
use App\Commande\Domain\ResultatPaiement;

interface PaiementGateway
{
    public function debiter(
        CommandeId $id,
        Montant $montant
    ): ResultatPaiement;
}
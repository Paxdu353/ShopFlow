<?php

namespace App\Commande\Application\Port;

use App\Commande\Domain\Commande;
use App\Commande\Domain\CommandeId;

interface CommandeRepository
{
    public function save(Commande $commande): void;

    public function findById(CommandeId $id): ?Commande;
}
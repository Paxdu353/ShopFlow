<?php

namespace App\Commande\Infrastructure\Doctrine;

use App\Commande\Application\Port\CommandeRepository;
use App\Commande\Domain\Commande;
use App\Commande\Domain\CommandeId;

final class DoctrineCommandeRepository implements CommandeRepository
{
    public function save(Commande $commande): void
    {
        // On branchera Doctrine ici ensuite.
    }

    public function findById(CommandeId $id): ?Commande
    {
        return null;
    }
}
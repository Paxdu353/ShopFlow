<?php

namespace App\Commande\Application;

use App\Commande\Application\Port\CommandeRepository;
use App\Commande\Application\Port\PaiementGateway;
use App\Commande\Domain\CommandeId;
use App\Commande\Domain\Exception\PanierVideException;
use App\Commande\Domain\Event\CommandePassee;
use CommandePassee as GlobalCommandePassee;
use Symfony\Component\Messenger\MessageBusInterface;

final class PasserCommandeHandler
{
    public function __construct(
        private CommandeRepository $commandeRepository,
        private PaiementGateway $paiementGateway,
        private MessageBusInterface $bus
    ) {
    }

    public function __invoke(CommandeId $id): void
    {
        // 1. Récupérer la commande
        $commande = $this->commandeRepository->findById($id);

        if ($commande === null) {
            throw new \RuntimeException('Commande introuvable');
        }

        // 2. Vérifier le panier
        if ($commande->getMontant()->getMontant() <= 0) {
            throw new PanierVideException();
        }

        // 3. Débiter le paiement
        $resultat = $this->paiementGateway->debiter(
            $commande->getId(),
            $commande->getMontant()
        );

        if (!$resultat->isSuccess()) {
            throw new \RuntimeException(
                $resultat->getMessage() ?? 'Paiement refusé'
            );
        }

        // 4. Enregistrer la commande
        $this->commandeRepository->save($commande);

        // 5. Préparer les lignes de commande
        $lignes = $commande->getLignes();

        // 6. Publier l'événement métier
        $this->bus->dispatch(
            new GlobalCommandePassee(
                $commande->getId()->getId(),
                $lignes,
                new \DateTimeImmutable()
            )
        );
    }
}

<?php

namespace App\Commande\Application\Handler;

use App\Commande\Application\Command\PasserCommande;
use CommandePassee;

class PasserCommandeHandler
{
    public function __construct(
        private StockService $stockService,
        private FactureService $factureService,
        private MessageBusInterface $eventBus
    ) {
    }

    public function __invoke(PasserCommande $command): void
    {
        // 1. Décrémenter le stock
        $this->stockService->decrementer(
            $command->getProduitId(),
            $command->getQuantite()
        );

        // 2. Générer la facture
        $facture = $this->factureService->generer(
            $command->getCommandeId()
        );

        // 3. Déclencher l'événement de commande passée
        $this->eventBus->dispatch(
            new CommandePassee(
                $command->getCommandeId(),
                $facture->getId()
            )
        );
    }
}
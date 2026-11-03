<?php

namespace App\Commande\Domain;

use App\Commande\Domain\Exception\PanierVideException;

class Commande
{
    private CommandeId $id;
    private Montant $montant;

    /** @var LigneCommande[] */
    private array $lignes;

    private bool $passee = false;

    public function __construct(
        CommandeId $id,
        Montant $montant,
        array $lignes = []
    ) {
        $this->id = $id;
        $this->montant = $montant;
        $this->lignes = $lignes;
    }

    public function getId(): CommandeId
    {
        return $this->id;
    }

    public function getMontant(): Montant
    {
        return $this->montant;
    }

    public function getLignes(): array
    {
        return $this->lignes;
    }

    public function estPassee(): bool
    {
        return $this->passee;
    }

    public function passer(): void
    {
        if (empty($this->lignes)) {
            throw new PanierVideException();
        }

        if ($this->passee) {
            throw new \DomainException(
                'Cette commande est déjà passée.'
            );
        }

        $this->passee = true;
    }
}
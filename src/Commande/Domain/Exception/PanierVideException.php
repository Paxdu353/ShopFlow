<?php

namespace App\Commande\Domain\Exception;

final class PanierVideException extends \DomainException
{
    public function __construct(
        string $message = 'Impossible de passer une commande avec un panier vide.'
    ) {
        parent::__construct($message);
    }
}
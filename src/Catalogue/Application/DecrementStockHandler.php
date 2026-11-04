<?php 
namespace App\Catalogue\Application;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Commande\Domain\Event\CommandePassee;

#[AsMessageHandler]
final class DecrementerStockHandler
{
public function __invoke(CommandePassee $e): void
{ /* idempotent : ignore si déjà traité */ }
}
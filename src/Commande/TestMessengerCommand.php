<?php

namespace App\Commande;

use App\Commande\Domain\Event\CommandePassee;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:test-messenger',
    description: 'Teste l’envoi de CommandePassee avec Symfony Messenger'
)]
final class TestMessengerCommand extends Command
{
    public function __construct(
        private MessageBusInterface $bus
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $this->bus->dispatch(
            new CommandePassee(
                'TEST-001',
                [],
                new \DateTimeImmutable()
            )
        );

        $output->writeln('CommandePassee envoyé dans Messenger.');

        return Command::SUCCESS;
    }
}
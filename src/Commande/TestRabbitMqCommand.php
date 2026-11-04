<?php

namespace App\Commande;

use App\Commande\Domain\Event\CommandePassee;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:test-rabbitmq',
    description: 'Envoie un événement de test dans RabbitMQ'
)]
final class TestRabbitMqCommand extends Command
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
        $event = new CommandePassee(
            'COMMANDE-TEST-001',
            [],
            new \DateTimeImmutable()
        );

        $this->bus->dispatch($event);

        $output->writeln('<info>Événement envoyé à Messenger !</info>');

        return Command::SUCCESS;
    }
}

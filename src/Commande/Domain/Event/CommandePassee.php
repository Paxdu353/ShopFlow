<?php
final class CommandePassee
    {
        public function __construct(
            public readonly string $commandeId,
            public readonly array $lignes,
            public readonly \DateTimeImmutable $passeeLe,
        ) {}
    }
<?php

declare(strict_types=1);

namespace Tdc\PdoMessengerTransport\Transport;

use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

final class PdoTransportFactory implements TransportFactoryInterface
{
    public function __construct(
        private ?\PDO $pdo = null,
        private string $tableName = 'messenger_messages',
    ) {
    }

    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        $queueName = $options['queue_name'] ?? 'default';
        $tableName = $options['table_name'] ?? $this->tableName;
        $pdo = $this->pdo;

        if (!$pdo instanceof \PDO) {
            throw new \InvalidArgumentException('You must provide a PDO instance or configure one in the transport factory.');
        }

        return new PdoTransport(
            new PdoSender($pdo, $serializer, $queueName, $tableName),
            new PdoReceiver($pdo, $serializer, $queueName, $tableName),
        );
    }

    public function supports(string $dsn, array $options): bool
    {
        return str_starts_with($dsn, 'pdoqueue://');
    }
}
<?php

declare(strict_types=1);

namespace Tdc\PdoMessengerTransport\Transport;

use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

#[AsTaggedItem('messenger.transport_factory')]
final class PdoTransportFactory implements TransportFactoryInterface
{
    public function __construct(
        private \PDO $pdo,
    ) {
    }

    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        $queueName = $options['queue_name'] ?? 'default';

        return new PdoTransport(
            new PdoSender($this->pdo, $serializer, $queueName),
            new PdoReceiver($this->pdo, $serializer, $queueName),
        );
    }

    public function supports(string $dsn, array $options): bool
    {
        return str_starts_with($dsn, 'pdoqueue://');
    }
}
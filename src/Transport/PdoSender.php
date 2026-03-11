<?php

declare(strict_types=1);

namespace Tdc\PdoMessengerTransport\Transport;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class PdoSender implements SenderInterface
{
    public function __construct(
        private \PDO $pdo,
        private SerializerInterface $serializer,
        private string $queueName = 'default',
    ) {
    }

    public function send(Envelope $envelope): Envelope
    {
        $encoded = $this->serializer->encode($envelope);

        $stmt = $this->pdo->prepare(
            'INSERT INTO messenger_messages (body, headers, queue_name, available_at, created_at)
             VALUES (:body, :headers, :queue_name, :available_at, :created_at)'
        );

        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $stmt->execute([
            'body' => $encoded['body'],
            'headers' => json_encode($encoded['headers'], JSON_THROW_ON_ERROR),
            'queue_name' => $this->queueName,
            'available_at' => $now,
            'created_at' => $now,
        ]);

        return $envelope;
    }
}
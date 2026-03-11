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
        private string $tableName = 'messenger_messages',
    ) {
    }

    public function send(Envelope $envelope): Envelope
    {
        $encoded = $this->serializer->encode($envelope);

        $stmt = $this->pdo->prepare(
            sprintf(
                'INSERT INTO %s (body, headers, queue_name, available_at, created_at)
                 VALUES (:body, :headers, :queue_name, :available_at, :created_at)',
                $this->tableName
            )
        );

        $now = new \DateTimeImmutable();
        $availableAt = $now->format('Y-m-d H:i:s');
        $createdAt = $now->format('Y-m-d H:i:s');

        $delayStamp = $envelope->last(\Symfony\Component\Messenger\Stamp\DelayStamp::class);
        if ($delayStamp instanceof \Symfony\Component\Messenger\Stamp\DelayStamp) {
            $availableAt = $now->modify(sprintf('+%d milliseconds', $delayStamp->getDelay()))->format('Y-m-d H:i:s');
        }

        $stmt->execute([
            'body' => $encoded['body'],
            'headers' => json_encode($encoded['headers'], JSON_THROW_ON_ERROR),
            'queue_name' => $this->queueName,
            'available_at' => $availableAt,
            'created_at' => $createdAt,
        ]);

        return $envelope;
    }
}
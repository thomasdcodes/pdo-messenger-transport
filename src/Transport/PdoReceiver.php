<?php

declare(strict_types=1);

namespace Tdc\PdoMessengerTransport\Transport;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

final class PdoReceiver implements ReceiverInterface
{
    public function __construct(
        private \PDO $pdo,
        private SerializerInterface $serializer,
        private string $queueName = 'default',
    ) {
    }

    public function get(): iterable
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                'SELECT id, body, headers
                 FROM messenger_messages
                 WHERE queue_name = :queue_name
                   AND delivered_at IS NULL
                   AND available_at <= :now
                 ORDER BY id ASC
                 LIMIT 1
                 FOR UPDATE'
            );

            $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

            $stmt->execute([
                'queue_name' => $this->queueName,
                'now' => $now,
            ]);

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                $this->pdo->commit();
                return [];
            }

            $update = $this->pdo->prepare(
                'UPDATE messenger_messages
                 SET delivered_at = :now
                 WHERE id = :id'
            );

            $update->execute([
                'id' => $row['id'],
                'now' => $now,
            ]);

            $this->pdo->commit();

            $headers = json_decode($row['headers'], true, 512, JSON_THROW_ON_ERROR);

            $envelope = $this->serializer->decode([
                'body' => $row['body'],
                'headers' => $headers,
            ]);

            yield $envelope->with(
                new PdoReceivedStamp((int) $row['id'])
            );
        } catch (\Throwable $e) {
            $this->pdo->rollBack();

            if ($e instanceof MessageDecodingFailedException) {
                throw $e;
            }

            throw $e;
        }
    }

    public function ack(Envelope $envelope): void
    {
        $id = $this->getMessageId($envelope);

        $stmt = $this->pdo->prepare('DELETE FROM messenger_messages WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function reject(Envelope $envelope): void
    {
        $id = $this->getMessageId($envelope);

        $stmt = $this->pdo->prepare('DELETE FROM messenger_messages WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    private function getMessageId(Envelope $envelope): int
    {
        $stamp = $envelope->last(PdoReceivedStamp::class);

        if (!$stamp instanceof PdoReceivedStamp) {
            throw new \RuntimeException('Missing PdoReceivedStamp.');
        }

        return $stamp->messageId;
    }
}
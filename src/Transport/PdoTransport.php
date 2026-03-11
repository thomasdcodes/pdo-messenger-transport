<?php

declare(strict_types=1);

namespace Tdc\PdoMessengerTransport\Transport;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\TransportInterface;

final class PdoTransport implements TransportInterface
{
    public function __construct(
        private PdoSender $sender,
        private PdoReceiver $receiver,
    ) {
    }

    public function get(): iterable
    {
        return $this->receiver->get();
    }

    public function ack(Envelope $envelope): void
    {
        $this->receiver->ack($envelope);
    }

    public function reject(Envelope $envelope): void
    {
        $this->receiver->reject($envelope);
    }

    public function send(Envelope $envelope): Envelope
    {
        return $this->sender->send($envelope);
    }
}
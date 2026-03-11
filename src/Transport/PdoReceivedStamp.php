<?php

declare(strict_types=1);

namespace Tdc\PdoMessengerTransport\Transport;

use Symfony\Component\Messenger\Stamp\StampInterface;

final readonly class PdoReceivedStamp implements StampInterface
{
    public function __construct(public int $messageId)
    {
    }
}
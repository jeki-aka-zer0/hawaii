<?php

declare(strict_types=1);

namespace App\Domain\Shared\Repository;

use App\Domain\Shared\Entity\AbstractId;
use DomainException;
use Throwable;

final class EntityNotFoundException extends DomainException
{
    public function __construct(
        readonly public AbstractId $id,
        string $message,
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function byId(AbstractId $id, string $message): self
    {
        return new self($id, $message);
    }
}

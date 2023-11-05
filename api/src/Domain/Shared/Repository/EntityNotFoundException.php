<?php

declare(strict_types=1);

namespace App\Domain\Shared\Repository;

use App\Domain\Shared\Entity\AbstractId;
use App\Domain\Shared\Util\Str;
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

    public static function byId(AbstractId $id, string $entity): self
    {
        return new self($id, sprintf('%s not found', (new Str($entity))->low()->upFirst()));
    }
}

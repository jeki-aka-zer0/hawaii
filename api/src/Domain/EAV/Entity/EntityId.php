<?php

declare(strict_types=1);

namespace App\Domain\EAV\Entity;

use JetBrains\PhpStorm\Pure;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

final class EntityId
{
    private string $value;

    public function __construct(string $value)
    {
        Assert::uuid($value);
        $this->value = mb_strtolower($value);
    }

    #[Pure]
    public function __toString(): string
    {
        return $this->getValue();
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public function getValue(): string
    {
        return $this->value;
    }
}

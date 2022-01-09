<?php

declare(strict_types=1);

namespace App\Domain\Shared\Entity;

use JetBrains\PhpStorm\Pure;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

abstract class AbstractId
{
    public function __construct(private string $value)
    {
        Assert::uuid($value);
        $this->value = mb_strtolower($value);
    }

    #[Pure]
    public function __toString(): string
    {
        return $this->getValue();
    }

    public static function generate(): static
    {
        return new static(Uuid::uuid4()->toString());
    }

    public function getValue(): string
    {
        return $this->value;
    }
}

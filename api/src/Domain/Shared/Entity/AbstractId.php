<?php

declare(strict_types=1);

namespace App\Domain\Shared\Entity;

use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

abstract class AbstractId
{
    public function __construct(private string $val)
    {
        Assert::uuid($val);
        $this->val = mb_strtolower($val);
    }

    public function __toString(): string
    {
        return $this->getVal();
    }

    public static function generate(): static
    {
        return new static(Uuid::uuid4()->toString());
    }

    public function getVal(): string
    {
        return $this->val;
    }

    public function isEqual(self $other): bool
    {
        return $this->getVal() === $other->getVal();
    }
}

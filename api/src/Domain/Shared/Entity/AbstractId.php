<?php

declare(strict_types=1);

namespace App\Domain\Shared\Entity;

use App\Domain\Shared\Util\Str;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

abstract class AbstractId
{
    public abstract function getField(): string;

    public function __construct(private string $val)
    {
        Assert::uuid($val, sprintf('%s is invalid identifier', (new Str(static::getField()))->humanize()->upFirst()));
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

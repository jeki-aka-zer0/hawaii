<?php

declare(strict_types=1);

namespace App\Domain\Shared\Util;

final class Str
{
    private const MAX_STR_LEN = 15;

    public function __construct(private $str)
    {
    }

    public function trunc(int $maxLen = self::MAX_STR_LEN, string $ending = '...'): self
    {
        if (mb_strlen($this->str) >= $maxLen) {
            $this->str = rtrim(substr($this->str, 0, $maxLen)).$ending;
        }

        return $this;
    }

    public function low(): self
    {
        $this->str = mb_strtolower($this->str);

        return $this;
    }

    public function trim(): self
    {
        $this->str = trim($this->str);

        return $this;
    }

    public function upFirst(): self
    {
        $this->str = ucfirst($this->str);

        return $this;
    }

    public function __toString(): string
    {
        return $this->str;
    }
}
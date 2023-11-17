<?php

declare(strict_types=1);

namespace App\Tests\Shared;

use Webmozart\Assert\Assert;

trait AssertTrait
{
    private const TYPE_UUID = 'uuid';

    private function assertArray(array $expected, array $actual): void
    {
        if (0 === count($expected)) {
            Assert::eq($actual, $expected);
            return;
        }

        foreach ($expected as $expectedKey => $expectedVal) {
            if (is_array($expectedVal)) {
                $this->assertArray($expectedVal, $actual[$expectedKey]);
            } elseif ($expectedVal === self::TYPE_UUID) {
                Assert::uuid($actual[$expectedKey]);
            } else {
                Assert::eq($actual[$expectedKey], $expectedVal);
            }
        }
    }
}
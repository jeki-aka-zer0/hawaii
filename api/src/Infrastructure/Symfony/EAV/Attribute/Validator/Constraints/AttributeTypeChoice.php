<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\EAV\Attribute\Validator\Constraints;

use App\Domain\EAV\Attribute\Entity\AttributeType;
use Attribute;
use Symfony\Component\Validator\Constraints\Choice;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class AttributeTypeChoice extends Choice
{
    public function __construct(
        $callback = null,
        bool $multiple = null,
        bool $strict = null,
        int $min = null,
        int $max = null,
        string $message = null,
        string $multipleMessage = null,
        string $minMessage = null,
        string $maxMessage = null,
        $groups = null,
        $payload = null,
        array $options = []
    ) {
        parent::__construct(
            AttributeType::cases(),
            $callback,
            $multiple,
            $strict,
            $min,
            $max,
            $message,
            $multipleMessage,
            $minMessage,
            $maxMessage,
            $groups,
            $payload,
            $options
        );
    }
}

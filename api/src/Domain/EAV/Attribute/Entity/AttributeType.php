<?php

declare(strict_types=1);

namespace App\Domain\EAV\Attribute\Entity;

enum AttributeType: string
{
    case String = 'string';
    case Int = 'int';
}

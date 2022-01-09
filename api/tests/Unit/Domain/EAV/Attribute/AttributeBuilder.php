<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\EAV\Attribute;

use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeId;

final class AttributeBuilder
{
    public const TEST_EXISTENT_NAME = 'Color';

    public function build(): Attribute
    {
        return new Attribute(AttributeId::generate(), self::TEST_EXISTENT_NAME);
    }
}

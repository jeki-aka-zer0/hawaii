<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\EAV\Attribute;

use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Attribute\Entity\AttributeType;

final class AttributeBuilder
{
    public const TEST_EXISTENT_NAME = 'Color';

    public function build(AttributeId $attributeId = null): Attribute
    {
        return new Attribute($attributeId ?? AttributeId::generate(), self::TEST_EXISTENT_NAME, AttributeType::String);
    }
}

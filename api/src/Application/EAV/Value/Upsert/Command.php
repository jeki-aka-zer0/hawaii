<?php

declare(strict_types=1);

namespace App\Application\EAV\Value\Upsert;

use App\Infrastructure\UI\Web\Request\CommandInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final class Command implements CommandInterface
{
    #[Assert\NotBlank, Assert\Uuid, SerializedName('entity_id')]
    public string $entityId;

    #[Assert\NotBlank, Assert\Uuid, SerializedName('attribute_id')]
    public string $attributeId;

    #[Assert\NotBlank, Assert\Type(['string', 'int'])]
    public string|int $value;
}

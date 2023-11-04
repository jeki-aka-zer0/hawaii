<?php

declare(strict_types=1);

namespace App\Application\EAV\Entity\Read;

use App\Domain\EAV\Entity\Entity\EntityId;
use App\Infrastructure\UI\Web\Request\QueryInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final class QueryOne implements QueryInterface
{
    #[Assert\NotBlank, Assert\Uuid, SerializedName('entityId')]
    private string $entityId;

    public function getEntityId(): EntityId
    {
        return new EntityId($this->entityId);
    }

    public function setEntityId(string $entityId): void
    {
        $this->entityId = $entityId;
    }
}

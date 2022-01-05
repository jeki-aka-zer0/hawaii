<?php

declare(strict_types=1);

namespace App\Application\EAV\Entity\Read;

use App\Infrastructure\UI\Web\Request\QueryInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final class Query implements QueryInterface
{
    #[Assert\Length(min: 2, max: 255), SerializedName('camel_case')]
    public ?string $name = null;
}

<?php

declare(strict_types=1);

namespace App\Application\EAV\Attribute\Read;

use App\Infrastructure\UI\Web\Request\QueryInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class Query implements QueryInterface
{
    #[Assert\Length(min: 2, max: 255)]
    public ?string $name = null;
}

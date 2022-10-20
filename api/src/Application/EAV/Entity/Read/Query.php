<?php

declare(strict_types=1);

namespace App\Application\EAV\Entity\Read;

use App\Infrastructure\UI\Web\Request\QueryListInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class Query implements QueryListInterface
{
    #[Assert\Length(min: 2, max: 255)]
    public ?string $name = null;

    #[Assert\PositiveOrZero]
    public int $offset = 0;

    #[Assert\GreaterThanOrEqual(1)]
    #[Assert\LessThanOrEqual(1000)]
    public int $limit = 100;

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'offset' => $this->offset,
            'limit' => $this->limit,
        ];
    }
}

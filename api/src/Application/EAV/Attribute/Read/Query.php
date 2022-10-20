<?php

declare(strict_types=1);

namespace App\Application\EAV\Attribute\Read;

use App\Infrastructure\UI\Web\Request\QueryListInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class Query implements QueryListInterface
{
    #[Assert\Length(min: 2, max: 255)]
    public ?string $name = null;

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Application\EAV\Entity\Create;

use App\Application\Shared\Field;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Infrastructure\UI\Web\Request\CommandInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class Command implements CommandInterface
{
    public const FIELD_NAME = 'name';

    #[Assert\NotBlank, Assert\Length(min: 2, max: 255)]
    public string $name;

    #[Assert\Type('string')]
    public ?string $description = null;

    public function getNameField(): Field
    {
        return new Field(Entity::LABEL, self::FIELD_NAME, $this->name);
    }

    public static function build(string $name, string $description = null): self
    {
        $cmd = new self();
        $cmd->name = $name;
        $cmd->description = $description;

        return $cmd;
    }
}

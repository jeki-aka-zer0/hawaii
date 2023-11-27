<?php

declare(strict_types=1);

namespace App\Application\EAV\Entity\Create;

use App\Application\Shared\Field;
use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Infrastructure\UI\Web\Request\CommandInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final class Command implements CommandInterface
{
    #[Assert\NotBlank, Assert\Length(min: 2, max: 255)]
    public string $name;

    #[Assert\Type('string')]
    public ?string $description = null;

    #[SerializedName(Attribute::KEY_ATTRS_VALUES)]
    public array $attributesValues = [];

    public function getNameField(): Field
    {
        return new Field(Entity::NAME, Entity::FIELD_NAME, $this->name);
    }

    public static function build(string $name, string $description = null, array $attrsVal = []): self
    {
        $cmd = new self();
        $cmd->name = $name;
        $cmd->description = $description;
        $cmd->attributesValues = $attrsVal;

        return $cmd;
    }
}

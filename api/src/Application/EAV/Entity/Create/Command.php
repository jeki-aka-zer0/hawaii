<?php

declare(strict_types=1);

namespace App\Application\EAV\Entity\Create;

use App\Application\Shared\Field;
use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Value\Entity\Value;
use App\Domain\Shared\Util\Str;
use App\Infrastructure\Doctrine\EAV\Attribute\AttributeIdType;
use App\Infrastructure\UI\Web\Request\CommandInterface;
use InvalidArgumentException;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final class Command implements CommandInterface
{
    #[Assert\NotBlank, Assert\Length(min: 2, max: 255)]
    public string $name;

    #[Assert\Type('string')]
    public ?string $description = null;

    #[Assert\All(new Assert\Collection([
        AttributeIdType::FIELD_ATTR_ID => [
            new Assert\Uuid,
        ],
        /** @see \App\Application\EAV\Attribute\Create\Command::$name */
        Attribute::FIELD_NAME => [
            new Assert\NotBlank,
            new Assert\Length(min: 2, max: 255),
        ],
        /** @see \App\Application\EAV\Value\Upsert\Command::$value */
        Value::FIELD_VALUE => [
            new Assert\NotBlank,
            new Assert\Type(['string', 'int']),
        ],
    ], allowMissingFields: true)), Assert\Count(max: 50), SerializedName(Attribute::KEY_ATTRS_VALUES)]
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

    /**
     * @return array{string, array{value: string|int, attribute_id: string}}
     * @throws InvalidArgumentException
     */
    public function getAttrsValMap(): array
    {
        $map = [];
        foreach ($this->attributesValues as $attrVal) {
            $attrName = (string)Str::build((string)($attrVal[Attribute::FIELD_NAME] ?? ''))->trim();
            if ('' === $attrName) {
                continue;
            }
            $val = (string)Str::build((string)($attrVal[Value::FIELD_VALUE] ?? ''))->trim();
            if ('' === $val) {
                continue;
            }
            if (is_numeric($val)) {
                $val = (int)$val;
            }
            $map[$attrName] = [
                Value::FIELD_VALUE => $val,
                AttributeIdType::FIELD_ATTR_ID => (string)Str::build(
                    (string)($attrVal[AttributeIdType::FIELD_ATTR_ID] ?? '')
                )
                    ->trim()
                    ->low(),
            ];
        }

        return $map;
    }
}

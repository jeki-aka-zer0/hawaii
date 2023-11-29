<?php

declare(strict_types=1);

namespace App\Application\EAV\Entity\Create;

use App\Application\EAV\Builder;
use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Attribute\Repository\AttributeRepository;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\EAV\Entity\Repository\EntityRepository;
use App\Domain\EAV\Value\Entity\Value;
use App\Domain\EAV\Value\Entity\ValueId;
use App\Domain\EAV\Value\Repository\ValueRepository;
use App\Domain\Flusher;
use App\Domain\Shared\Repository\FieldException;
use App\Domain\Shared\Util\Str;
use App\Infrastructure\Doctrine\EAV\Attribute\AttributeIdType;
use DateTimeImmutable;
use InvalidArgumentException;

final readonly class CommandHandler
{
    public function __construct(
        private EntityRepository $entities,
        private AttributeRepository $attrs,
        private ValueRepository $val,
        private Flusher $flusher
    ) {
    }

    public function handle(Command $cmd): EntityId
    {
        if ($this->entities->hasByName((string)(new Str($cmd->name))->trim()->low())) {
            throw FieldException::alreadyExists($cmd->getNameField());
        }

        $this->entities->add(
            $entity = new Entity(
                $entityId = EntityId::generate(),
                (string)(new Str($cmd->name))->trim(),
                (string)(new Str($cmd->description ?? ''))->trim() ?: null,
                new DateTimeImmutable(),
            )
        );

        try {
            foreach ($cmd->getAttrsValMap() as $attrName => $row) {
                $this->val->add(
                    new Value(
                        ValueId::generate(),
                        $entity,
                        $this->findAttrById($row[AttributeIdType::FIELD_ATTR_ID])
                        ?? $this->attrs->findByName($attrName)
                        ?? $this->createAttr($attrName, $row[Value::FIELD_VALUE]),
                        $row[Value::FIELD_VALUE],
                    )
                );
            }
        } catch (InvalidArgumentException $e) {
            throw FieldException::build(Attribute::KEY_ATTRS_VALUES, $e->getMessage());
        }

        $this->flusher->flush();

        return $entityId;
    }

    private function findAttrById(?string $attrId): ?Attribute
    {
        return empty($attrId) ? null : $this->attrs->find(new AttributeId($attrId));
    }

    private function createAttr(string $attrName, string|int $val): Attribute
    {
        $this->attrs->add(
            $attr = new Attribute(
                AttributeId::generate(),
                $attrName,
                Builder::getAttrTypeByVal($val),
                new DateTimeImmutable(),
            )
        );

        return $attr;
    }
}

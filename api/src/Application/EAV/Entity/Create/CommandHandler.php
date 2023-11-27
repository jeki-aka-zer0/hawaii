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
use RuntimeException;

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
            foreach ($cmd->attributesValues as $attrVal) {
                $attrName = (string)Str::build((string)($attrVal[Attribute::FIELD_NAME] ?? ''))->trim();
                if ('' === $attrName) {
                    continue;
                }
                $valRaw = (string)Str::build((string)($attrVal[Value::FIELD_VALUE] ?? ''))->trim();
                if ('' === $valRaw) {
                    continue;
                }
                if (is_numeric($valRaw)) {
                    $valRaw = (int)$valRaw;
                }

                $attrIdRaw = (string)Str::build((string)($attrVal[AttributeIdType::FIELD_ATTR_ID] ?? ''))->trim()->low();

                $attrId = $attr = null;
                if ('' !== $attrIdRaw) {
                    $attr = $this->attrs->find($attrId = new AttributeId($attrIdRaw));
                }

                if (null === $attr) {
                    $this->attrs->add(
                        $attr = new Attribute(
                            $attrId = AttributeId::generate(),
                            $attrName,
                            Builder::getAttrTypeByVal($valRaw),
                            new DateTimeImmutable(),
                        )
                    );
                }

                if (null === $attrId) {
                    throw new RuntimeException(
                        sprintf('%s is required', (new Str(AttributeIdType::FIELD_ATTR_ID))->humanize())
                    );
                }

                if (null === $attr) {
                    throw new RuntimeException(
                        sprintf('%s is required', (new Str(Attribute::NAME))->humanize())
                    );
                }

                $val = $this->val->findByEntityAndAttr($entityId, $attrId);
                if (null === $val) {
                    $this->val->add(
                        new Value(
                            ValueId::generate(),
                            $entity,
                            $attr,
                            $valRaw,
                        )
                    );
                } else {
                    $val->updateVal($valRaw);
                }
            }
        } catch (InvalidArgumentException $e) {
            throw FieldException::build(Attribute::KEY_ATTRS_VALUES, $e->getMessage());
        }

        $this->flusher->flush();

        return $entityId;
    }
}

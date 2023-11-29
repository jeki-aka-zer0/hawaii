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

        $alreadyAddedAttrs = [];
        try {
            foreach ($cmd->attributesValues as $attrVal) {
                $attrName = (string)Str::build((string)($attrVal[Attribute::FIELD_NAME] ?? ''))->trim();
                if ('' === $attrName) {
                    continue;
                }
                // until checkbox attribute is implemented, accept only one value for every attribute like radio button
                if (isset($alreadyAddedAttrs[$attrName])) {
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
                $attr = '' === $attrIdRaw
                    ? $this->attrs->findByName($attrName)
                    : $this->attrs->find(new AttributeId($attrIdRaw));

                if (null === $attr) {
                    $this->attrs->add(
                        $attr = new Attribute(
                            AttributeId::generate(),
                            $attrName,
                            Builder::getAttrTypeByVal($valRaw),
                            new DateTimeImmutable(),
                        )
                    );
                }

                if (null === $attr) {
                    throw new RuntimeException(
                        sprintf('%s is required', (new Str(Attribute::NAME))->humanize())
                    );
                }

                $this->val->add(
                    new Value(
                        ValueId::generate(),
                        $entity,
                        $attr,
                        $valRaw,
                    )
                );
                $alreadyAddedAttrs[$attrName] = true;
            }
        } catch (InvalidArgumentException $e) {
            throw FieldException::build(Attribute::KEY_ATTRS_VALUES, $e->getMessage());
        }

        $this->flusher->flush();

        return $entityId;
    }
}

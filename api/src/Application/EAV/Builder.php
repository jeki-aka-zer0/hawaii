<?php

declare(strict_types=1);

namespace App\Application\EAV;

use App\Application\EAV\Attribute\Create\Command as AttributeCommand;
use App\Application\EAV\Attribute\Create\CommandHandler as AttributeHandler;
use App\Application\EAV\Entity\Create\Command as EntityCommand;
use App\Application\EAV\Entity\Create\CommandHandler as EntityHandler;
use App\Application\EAV\Value\Upsert\Command as ValueCommand;
use App\Application\EAV\Value\Upsert\CommandHandler as ValueHandler;
use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\EAV\Value\Entity\Value;
use App\Domain\EAV\Value\Entity\ValueId;
use RuntimeException;

final readonly class Builder
{
    public const ENTITY_NAME_TO_DESC_MAP = [
        'Elasticsearch' => 'Is a search and analytics engine.',
        'Logstash' => 'Is a server‑side data processing pipeline that ingests data from multiple sources simultaneously, transforms it, and then sends it to a "stash" like Elasticsearch.',
        'Kibana' => 'Lets users visualize data with charts and graphs in Elasticsearch.',
        'Beat' => 'Is a family of lightweight, single-purpose data shippers. After adding this tool ELK was renamed to Elastic stack.',
        'Spoofing' => 'In the context of information security, and especially network security, a spoofing attack is a situation in which a person or program successfully identifies as another by falsifying data, to gain an illegitimate advantage.',
        'CGI' => 'A protocol for calling external software via a Web server to deliver dynamic content.',
        'FastCGI' => 'is a binary protocol for interfacing interactive programs with a web server. It is a variation on the earlier Common Gateway Interface (CGI). FastCGI\'s main aim is to reduce the overhead related to interfacing between web server and CGI programs, allowing a server to handle more web page requests per unit of time.',
        'Graceful degradation' => 'Is the ability of a computer, machine, electronic system or network to maintain limited functionality even when a large portion of it has been destroyed or rendered inoperative.',
    ];

    public const ATTRIBUTE_NAME_TO_TYPE_MAP = [
        'Keyword' => AttributeType::String,
        'Category' => AttributeType::String,
        'Importance' => AttributeType::Int,
        'Popularity' => AttributeType::Int,
        'Lang' => AttributeType::String,
    ];

    private const STR_VALUES = [
        'Repeat',
        'TODO',
        'Programming',
        'Interesting fact',
        'Education',
        'ELK',
        'CGI',
        'Algorithm',
        'EN',
        'FR',
        'RU',
        'UA',
    ];

    public function __construct(
        private EntityHandler $entityHandler,
        private AttributeHandler $attributeHandler,
        private ValueHandler $valueHandler,
    ) {
    }

    public function createAll(
        string $entityName = null,
        string $attributeName = null,
        AttributeType $attributeType = null,
        int|string $value = null,
        string $entityDescription = null,
    ): void {
        $entityId = $this->createEntity(
            $entityName ??= self::getRandomEntityName(),
            $entityDescription ?? self::ENTITY_NAME_TO_DESC_MAP[$entityName] ?? null,
        );
        /** @var AttributeType $attributeType */
        $attributeId = $this->createAttribute(
            $attributeName ??= self::getRandomAttributeName(),
            $attributeType ??=
            self::ATTRIBUTE_NAME_TO_TYPE_MAP[$attributeName] ??
            throw new RuntimeException(sprintf('Cannot detect %s type', Attribute::NAME))
        );
        $this->createValue($entityId, $attributeId, $value ?? self::getRandomValue($attributeType));
    }

    public function createEntity(string $name, string $description = null): EntityId
    {
        return $this->entityHandler->handle(EntityCommand::build($name, $description));
    }

    public function createAttribute(string $name, AttributeType $type): AttributeId
    {
        return $this->attributeHandler->handle(AttributeCommand::build($name, $type));
    }

    public function createValue(EntityId $entityId, AttributeId $attributeId, int|string $value): ValueId
    {
        return $this->valueHandler->handle(ValueCommand::build($entityId, $attributeId, $value));
    }

    public static function buildAttribute(AttributeId $id = null, AttributeType $type = AttributeType::String): Attribute
    {
        return new Attribute($id ?? AttributeId::generate(), self::getRandomAttributeName(), $type);
    }

    public static function buildEntity(EntityId $entityId = null): Entity
    {
        return new Entity(
            $entityId ?? EntityId::generate(),
            $name = self::getRandomEntityName(),
            self::ENTITY_NAME_TO_DESC_MAP[$name]
        );
    }

    public static function buildValue(Entity $entity = null, Attribute $attribute = null): Value
    {
        return new Value(
            ValueId::generate(),
            $entity ?? self::buildEntity(),
            $attribute ??= self::buildAttribute(),
            self::getRandomValue($attribute),
        );
    }

    public static function getRandomAttributeName(string $exclude = ''): string
    {
        return self::getRandomStrFromArray(self::ATTRIBUTE_NAME_TO_TYPE_MAP, $exclude);
    }

    public static function getRandomEntityName(string $exclude = ''): string
    {
        return self::getRandomStrFromArray(self::ENTITY_NAME_TO_DESC_MAP, $exclude);
    }

    private static function getRandomStrFromArray(array $array, string $exclude): string
    {
        $name = array_rand($array);

        return $exclude === $name ? self::getRandomStrFromArray($array, $exclude) : $name;
    }

    public static function getRandomValue(Attribute|AttributeType $attribute): string|int
    {
        return match (match ($attribute::class) {
            Attribute::class => $attribute->type,
            AttributeType::class => $attribute,
        }) {
            AttributeType::String => self::getRandomStrValue(),
            AttributeType::Int => self::getRandomIntValue(),
        };
    }

    public static function getRandomStrValue(): string
    {
        return self::STR_VALUES[array_rand(self::STR_VALUES)];
    }

    public static function getRandomIntValue(): int
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return random_int(PHP_INT_MIN, PHP_INT_MAX);
    }
}

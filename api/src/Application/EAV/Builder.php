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

    public const ATTR_NAME_TO_TYPE_MAP = [
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
        private AttributeHandler $attrHandler,
        private ValueHandler $valHandler,
    ) {
    }

    public function createAll(
        string $entityName = null,
        string $attrName = null,
        AttributeType $attrType = null,
        int|string $val = null,
        string $entityDescription = null,
    ): void {
        $entityId = $this->createEntity(
            $entityName ??= self::getRandEntityName(),
            $entityDescription ?? self::ENTITY_NAME_TO_DESC_MAP[$entityName] ?? null,
        );
        /** @var AttributeType $attrType */
        $attrId = $this->createAttr(
            $attrName ??= self::getRandAttrName(),
            $attrType ??=
            self::ATTR_NAME_TO_TYPE_MAP[$attrName] ??
            throw new RuntimeException(sprintf('Cannot detect %s type', Attribute::NAME))
        );
        $this->createVal($entityId, $attrId, $val ?? self::getRandVal($attrType));
    }

    public function createEntity(string $name, string $description = null): EntityId
    {
        return $this->entityHandler->handle(EntityCommand::build($name, $description));
    }

    public function createAttr(string $name, AttributeType $type): AttributeId
    {
        return $this->attrHandler->handle(AttributeCommand::build($name, $type));
    }

    public function createVal(EntityId $entityId, AttributeId $attrId, int|string $val): ValueId
    {
        return $this->valHandler->handle(ValueCommand::build($entityId, $attrId, $val));
    }

    public static function buildAttr(AttributeId $id = null, AttributeType $type = AttributeType::String): Attribute
    {
        return new Attribute($id ?? AttributeId::generate(), self::getRandAttrName(), $type);
    }

    public static function buildEntity(EntityId $entityId = null): Entity
    {
        return new Entity(
            $entityId ?? EntityId::generate(),
            $name = self::getRandEntityName(),
            self::ENTITY_NAME_TO_DESC_MAP[$name]
        );
    }

    public static function buildVal(Entity $entity = null, Attribute $attr = null): Value
    {
        return new Value(
            ValueId::generate(),
            $entity ?? self::buildEntity(),
            $attr ??= self::buildAttr(),
            self::getRandVal($attr),
        );
    }

    public static function getRandAttrName(string $exclude = ''): string
    {
        return self::getRandStrFromArray(self::ATTR_NAME_TO_TYPE_MAP, $exclude);
    }

    public static function getRandEntityName(string $exclude = ''): string
    {
        return self::getRandStrFromArray(self::ENTITY_NAME_TO_DESC_MAP, $exclude);
    }

    private static function getRandStrFromArray(array $array, string $exclude): string
    {
        $name = array_rand($array);

        return $exclude === $name ? self::getRandStrFromArray($array, $exclude) : $name;
    }

    public static function getRandVal(Attribute|AttributeType $attr): string|int
    {
        return match (match ($attr::class) {
            Attribute::class => $attr->type,
            AttributeType::class => $attr,
        }) {
            AttributeType::String => self::getRandStrValue(),
            AttributeType::Int => self::getRandIntValue(),
        };
    }

    public static function getRandStrValue(): string
    {
        return self::STR_VALUES[array_rand(self::STR_VALUES)];
    }

    public static function getRandIntValue(): int
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return random_int(PHP_INT_MIN, PHP_INT_MAX);
    }

    public static function getAttrTypeByVal(string|int $val): ?AttributeType
    {
        return match (true) {
            is_string($val) => AttributeType::String,
            is_int($val) => AttributeType::Int,
        };
    }
}

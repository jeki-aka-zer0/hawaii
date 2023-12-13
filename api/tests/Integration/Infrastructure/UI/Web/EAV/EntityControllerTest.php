<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\UI\Web\EAV;

use App\Application\EAV\Builder;
use App\Application\EAV\Entity\Read\Query;
use App\Application\EAV\Entity\Read\QueryHandler;
use App\Application\Shared\ListDTO;
use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Value\Entity\Value;
use App\Infrastructure\Doctrine\EAV\Entity\EntityIdType;
use App\Infrastructure\UI\Web\EAV\EntityController;
use App\Infrastructure\UI\Web\Response\Pagination\PaginationDecoratorDTO;
use App\Tests\Integration\Infrastructure\UI\Web\AbstractEndpointTestCase;
use App\Tests\Shared\AssertTrait;

final class EntityControllerTest extends AbstractEndpointTestCase
{
    use AssertTrait;

    private static EntityController $SUT;
    private static QueryHandler $queryHandler;
    private static Builder $builder;

    private static array $allResults;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$SUT = self::getContainer()->get(EntityController::class);
        self::$queryHandler = self::getContainer()->get(QueryHandler::class);
        self::$builder = self::getContainer()->get(Builder::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::$connection->executeQuery(sprintf('TRUNCATE %s, %s CASCADE', Entity::NAME, Attribute::NAME));
    }

    public function readDataProvider(): array
    {
        return [
            'read all' => [
                'queryParams' => [],
                'expected' => [
                    ListDTO::KEY_COUNT => 2,
                    ListDTO::KEY_RESULTS => self::getAllResults(),
                    PaginationDecoratorDTO::KEY_PREVIOUS => null,
                    PaginationDecoratorDTO::KEY_NEXT => null,
                ],
            ],
            'returns an empty result when no search matched' => [
                'queryParams' => [
                    Query::KEY_SEARCH => 'some non existent entity name or value',
                ],
                'expected' => self::EXPECTED_RESPONSE_NONE,
            ],
            'returns one entity found by its name' => [
                'queryParams' => [
                    Query::KEY_SEARCH => self::getAllResults()[0][Entity::FIELD_NAME],
                ],
                'expected' => [
                    ListDTO::KEY_COUNT => 1,
                    ListDTO::KEY_RESULTS => [self::getAllResults()[0]],
                    PaginationDecoratorDTO::KEY_PREVIOUS => null,
                    PaginationDecoratorDTO::KEY_NEXT => null,
                ],
            ],
            'returns one entity found by its value' => [
                'queryParams' => [
                    Query::KEY_SEARCH => self::getAllResults()[1][Attribute::KEY_ATTRS_VALUES][0][Value::FIELD_VALUE],
                ],
                'expected' => [
                    ListDTO::KEY_COUNT => 1,
                    ListDTO::KEY_RESULTS => [self::getAllResults()[1]],
                    PaginationDecoratorDTO::KEY_PREVIOUS => null,
                    PaginationDecoratorDTO::KEY_NEXT => null,
                ],
            ],
        ];
    }

    /**
     * @dataProvider readDataProvider
     */
    public function testRead(array $queryParams, array $expected): void
    {
        foreach (self::getAllResults() as $row) {
            self::$builder->createAll(
                $row[Entity::FIELD_NAME],
                $row[Attribute::KEY_ATTRS_VALUES][0][Attribute::FIELD_NAME],
                Builder::getAttrTypeByVal($val = $row[Attribute::KEY_ATTRS_VALUES][0][Value::FIELD_VALUE]),
                $val,
                $row[Entity::FIELD_DESCRIPTION],
            );
        }
        $query = Query::build($queryParams[Query::KEY_SEARCH] ?? null);

        $response = self::$SUT->read($query, self::$queryHandler);

        $this->assertArray($expected, $this->assertSuccessfulJson($response));
    }

    private static function getAllResults(): array
    {
        return self::$allResults ??= [
            [
                EntityIdType::FIELD_ENTITY_ID => self::TYPE_UUID,
                Entity::FIELD_NAME => $entityName1 = Builder::getRandEntityName(),
                Entity::FIELD_DESCRIPTION => Builder::ENTITY_NAME_TO_DESC_MAP[$entityName1],
                Attribute::KEY_ATTRS_VALUES => [
                    [
                        Attribute::FIELD_NAME => $attributeName1 = Builder::getRandAttrName(),
                        Value::FIELD_VALUE => $val1 = Builder::getRandVal(Builder::ATTR_NAME_TO_TYPE_MAP[$attributeName1]),
                    ],
                ],
            ],
            [
                EntityIdType::FIELD_ENTITY_ID => self::TYPE_UUID,
                Entity::FIELD_NAME => $entityName2 = Builder::getRandEntityName($entityName1),
                Entity::FIELD_DESCRIPTION => Builder::ENTITY_NAME_TO_DESC_MAP[$entityName2],
                Attribute::KEY_ATTRS_VALUES => [
                    [
                        Attribute::FIELD_NAME => $attributeName2 = Builder::getRandAttrName($attributeName1),
                        Value::FIELD_VALUE => Builder::getRandVal(Builder::ATTR_NAME_TO_TYPE_MAP[$attributeName2], $val1),
                    ],
                ],
            ],
        ];
    }

    public function testReadEntityWithoutAttrAndVal(): void
    {
        self::$builder->createEntity($name = Builder::getRandEntityName());

        $response = self::$SUT->read(new Query(), self::$queryHandler);

        $this->assertArray([
            ListDTO::KEY_COUNT => 1,
            ListDTO::KEY_RESULTS => [
                [
                    EntityIdType::FIELD_ENTITY_ID => self::TYPE_UUID,
                    Entity::FIELD_NAME => $name,
                    Entity::FIELD_DESCRIPTION => null,
                    Attribute::KEY_ATTRS_VALUES => [],
                ],
            ],
        ], $this->assertSuccessfulJson($response));
    }
}

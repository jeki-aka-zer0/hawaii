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
                'query' => new Query(),
                'expected' => [
                    ListDTO::KEY_COUNT => 3,
                    ListDTO::KEY_RESULTS => self::getAllResults(),
                    PaginationDecoratorDTO::KEY_PREVIOUS => null,
                    PaginationDecoratorDTO::KEY_NEXT => null,
                ],
            ],
            'returns an empty result when no search matched' => [
                'query' => (static function (): Query {
                    $q = new Query();
                    $q->search = 'some non existent entity name or value';

                    return $q;
                })(),
                'expected' => self::EXPECTED_RESPONSE_NONE,
            ],
            'returns one entity found by its name' => [
                'query' => (static function (): Query {
                    $q = new Query();
                    $q->search = self::getAllResults()[0][Entity::FIELD_NAME];

                    return $q;
                })(),
                'expected' => [
                    ListDTO::KEY_COUNT => 1,
                    ListDTO::KEY_RESULTS => [self::getAllResults()[0]],
                    PaginationDecoratorDTO::KEY_PREVIOUS => null,
                    PaginationDecoratorDTO::KEY_NEXT => null,
                ],
            ],
            'returns one entity found by its value' => [
                'query' => (static function (): Query {
                    $q = new Query();
                    $q->search = self::getAllResults()[1][Attribute::KEY_ATTRS_VALUES][0][Value::FIELD_VALUE];

                    return $q;
                })(),
                'expected' => [
                    ListDTO::KEY_COUNT => 1,
                    ListDTO::KEY_RESULTS => [self::getAllResults()[1]],
                    PaginationDecoratorDTO::KEY_PREVIOUS => null,
                    PaginationDecoratorDTO::KEY_NEXT => null,
                ],
            ],
            'returns one of several entities with limit and offset' => [
                'query' => (static function (): Query {
                    $q = new Query();
                    $q->limit = 1;
                    $q->offset = 1;

                    return $q;
                })(),
                'expected' => [
                    ListDTO::KEY_COUNT => 3,
                    ListDTO::KEY_RESULTS => [self::getAllResults()[1]],
                    PaginationDecoratorDTO::KEY_PREVIOUS => 'http://localhost/eav/entity?offset=0&limit=1',
                    PaginationDecoratorDTO::KEY_NEXT => 'http://localhost/eav/entity?offset=2&limit=1',
                ],
            ],
        ];
    }

    /**
     * @dataProvider readDataProvider
     */
    public function testRead(Query $query, array $expected): void
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

        $response = self::$SUT->read($query, self::$queryHandler);

        $this->assertArray($expected, $this->assertSuccessfulJson($response));
    }

    private static function getAllResults(): array
    {
        if (empty(self::$allResults)) {
            $entityNames = [];
            $attributeNames = [];
            for ($i = 0; $i < 3; $i++) {
                $entityName = Builder::getRandEntityName($entityNames);
                $entityNames[] = $entityName;
                $attributeName = Builder::getRandAttrName($attributeNames);
                $attributeNames[] = $attributeName;

                self::$allResults[] = [
                    EntityIdType::FIELD_ENTITY_ID => self::TYPE_UUID,
                    Entity::FIELD_NAME => $entityName,
                    Entity::FIELD_DESCRIPTION => Builder::ENTITY_NAME_TO_DESC_MAP[$entityName],
                    Attribute::KEY_ATTRS_VALUES => [
                        [
                            Attribute::FIELD_NAME => $attributeName,
                            Value::FIELD_VALUE => Builder::getRandVal(Builder::ATTR_NAME_TO_TYPE_MAP[$attributeName]),
                        ],
                    ],
                ];
            }
            usort(self::$allResults, function ($a, $b){
                return strnatcmp($a[Entity::FIELD_NAME], $b[Entity::FIELD_NAME]);
            });
        }

        return self::$allResults;
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

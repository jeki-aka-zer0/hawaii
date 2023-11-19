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
use App\Tests\Integration\Infrastructure\UI\Web\AbstractEndpointTestCase;
use App\Tests\Shared\AssertTrait;

final class EntityControllerTest extends AbstractEndpointTestCase
{
    use AssertTrait;

    private static EntityController $SUT;
    private static QueryHandler $queryHandler;
    private static Builder $builder;

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
                    ListDTO::KEY_COUNT => 1,
                    ListDTO::KEY_RESULTS => [
                        [
                            EntityIdType::FIELD_ENTITY_ID => self::TYPE_UUID,
                            Entity::FIELD_NAME => $entityName = Builder::getRandEntityName(),
                            Entity::FIELD_DESCRIPTION => Builder::ENTITY_NAME_TO_DESC_MAP[$entityName],
                            QueryHandler::KEY_ATTRS_VALUES => [
                                [
                                    Attribute::FIELD_NAME => $attributeName = Builder::getRandAttrName(),
                                    Value::FIELD_VALUE => Builder::getRandVal(Builder::ATTR_NAME_TO_TYPE_MAP[$attributeName]),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'read returns an empty result when no name matched' => [
                'queryParams' => [
                    Entity::FIELD_NAME => 'some non existent entity name',
                ],
                'expected' => self::EXPECTED_RESPONSE_NONE,
            ],
        ];
    }

    /**
     * @dataProvider readDataProvider
     */
    public function testRead(array $queryParams, array $expected): void
    {
        $val = $expected[ListDTO::KEY_RESULTS][0][QueryHandler::KEY_ATTRS_VALUES][0][Value::FIELD_VALUE] ?? null;
        self::$builder->createAll(
            $expected[ListDTO::KEY_RESULTS][0][Entity::FIELD_NAME] ?? null,
                $expected[ListDTO::KEY_RESULTS][0][QueryHandler::KEY_ATTRS_VALUES][0][Attribute::FIELD_NAME] ?? null,
                $val ? Builder::getAttrTypeByVal($val) : null,
            $val,
            $expected[ListDTO::KEY_RESULTS][0][Entity::FIELD_DESCRIPTION] ?? null
        );
        $query = Query::build($queryParams[Entity::FIELD_NAME] ?? null);

        $response = self::$SUT->read($query, self::$queryHandler);

        $this->assertArray($expected, $this->assertSuccessfulJson($response));
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
                    QueryHandler::KEY_ATTRS_VALUES => [],
                ],
            ],
        ], $this->assertSuccessfulJson($response));
    }
}

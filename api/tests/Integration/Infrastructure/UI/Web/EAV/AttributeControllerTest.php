<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\UI\Web\EAV;

use App\Application\EAV\Attribute\Read\Query;
use App\Application\EAV\Attribute\Read\QueryHandler;
use App\Application\EAV\Builder;
use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Infrastructure\Doctrine\EAV\Attribute\AttributeIdType;
use App\Infrastructure\UI\Web\EAV\AttributeController;
use App\Tests\Integration\Infrastructure\UI\Web\AbstractEndpointTestCase;
use App\Tests\Shared\AssertTrait;

final class AttributeControllerTest extends AbstractEndpointTestCase
{
    use AssertTrait;

    private static AttributeController $SUT;
    private static QueryHandler $queryHandler;
    private static Builder $builder;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$SUT = self::getContainer()->get(AttributeController::class);
        self::$queryHandler = self::getContainer()->get(QueryHandler::class);
        self::$builder = self::getContainer()->get(Builder::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::$connection->executeQuery(sprintf('TRUNCATE %s, %s CASCADE', Entity::NAME, Attribute::NAME));
    }

    public function testRead(): void
    {
        // create 2 entities with two different values of the same attribute
        self::$builder->createVal(
            self::$builder->createEntity($entityName1 = Builder::getRandEntityName()),
            $attrId = self::$builder->createAttr($attrName = Builder::getRandAttrName(), AttributeType::Int),
            $val1 = 1,
        );
        self::$builder->createVal(
            self::$builder->createEntity(Builder::getRandEntityName([$entityName1])),
            $attrId,
            $val2 = 2,
        );

        $response = self::$SUT->read(new Query, self::$queryHandler);

        $this->assertArray([
            AttributeController::KEY_ATTRIBUTES => [
                 [
                     AttributeIdType::FIELD_ATTR_ID => self::TYPE_UUID,
                     Attribute::FIELD_NAME => $attrName,
                     QueryHandler::KEY_VAL => [$val1, $val2],
                 ]
            ],
        ], $this->assertSuccessfulJson($response));
    }

    public function testReadReturnsEmptyResultWhenNoNameMatched(): void
    {
        self::$builder->createAll();

        $query = Query::build('some non existent entity name');

        $response = self::$SUT->read($query, self::$queryHandler);

        self::assertEquals([AttributeController::KEY_ATTRIBUTES => []], $this->assertSuccessfulJson($response));
    }
}

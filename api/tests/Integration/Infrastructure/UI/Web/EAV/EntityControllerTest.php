<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\UI\Web\EAV;

use App\Application\EAV\Builder;
use App\Application\EAV\Entity\Read\Query;
use App\Application\EAV\Entity\Read\QueryHandler;
use App\Application\Shared\ListDTO;
use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Value\Entity\Value;
use App\Infrastructure\Doctrine\EAV\Entity\EntityIdType;
use App\Infrastructure\UI\Web\EAV\EntityController;
use App\Tests\Integration\Infrastructure\UI\Web\AbstractEndpointTestCase;
use App\Tests\Shared\AssertTrait;

final class EntityControllerTest extends AbstractEndpointTestCase
{
    use AssertTrait;

    private const ENTITY_NAME = 'Hello';
    private const ATTRIBUTE_NAME = 'Language';
    private const VALUE = 'en';
    private const EXPECTED_RESPONSE_ALL = [
        ListDTO::KEY_COUNT => 1,
        ListDTO::KEY_RESULTS => [
            [
                EntityIdType::FIELD_ENTITY_ID => self::TYPE_UUID,
                Entity::FIELD_NAME => self::ENTITY_NAME,
                Entity::FIELD_DESCRIPTION => null,
                QueryHandler::KEY_ATTRIBUTES_VALUES => [
                    [
                        Attribute::FIELD_NAME => self::ATTRIBUTE_NAME,
                        Value::FIELD_VALUE => self::VALUE,
                    ],
                ],
            ],
        ],
    ];
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
        self::$connection->executeQuery(sprintf('TRUNCATE %s CASCADE', Entity::NAME));
    }

    public function readDataProvider(): array
    {
        return [
            'read all' => [
                'queryParams' => [],
                'expected' => self::EXPECTED_RESPONSE_ALL,
            ],
            'read return an empty result when no name matched' => [
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
        self::$builder
            ->createAll(self::ENTITY_NAME, self::ATTRIBUTE_NAME, AttributeType::String, self::VALUE);
        $query = new Query();
        $query->name = $queryParams[Entity::FIELD_NAME] ?? null;

        $response = self::$SUT->read($query, self::$queryHandler);

        $this->assertArray($expected, $this->assertSuccessfulJson($response));
    }
}

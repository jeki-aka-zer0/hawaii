<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\EAV\Entity\Read;

use App\Application\EAV\Builder;
use App\Application\EAV\Entity\Read\Query;
use App\Application\EAV\Entity\Read\QueryHandler;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Infrastructure\Doctrine\EAV\Entity\EntityIdType;
use App\Tests\Integration\BaseIntegrationTest;

final class QueryHandlerTest extends BaseIntegrationTest
{
    private static QueryHandler $SUT;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$SUT = self::getContainer()->get(QueryHandler::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::$connection->executeQuery('TRUNCATE entity CASCADE');
    }

    public function testRead(): void
    {
        self::getContainer()
            ->get(Builder::class)
            ->buildAll($entityName = 'Hello', $attributeName = 'Language', AttributeType::String, $value = 'en');

        $res = self::$SUT->read(new Query());

        $this->assertEquals(1, $res->count);
        $this->assertNotEmpty($res->results[0][EntityIdType::NAME]);
        $this->assertEquals($entityName, $res->results[0]['name']);
        $this->assertCount(1, $res->results[0]['attributes_values']);
        $this->assertEquals($attributeName, $res->results[0]['attributes_values'][0]['name']);
        $this->assertEquals($value, $res->results[0]['attributes_values'][0]['value']);
    }

    public function testReadReturnEmptyResultWhenNoNameMatched(): void
    {
        $query = new Query();
        $query->name = 'some non existent entity name';

        $res = self::$SUT->read($query);

        $this->assertEquals(0, $res->count);
    }
}

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
    public function testRead(): void
    {
        self::$connection->executeQuery('TRUNCATE entity CASCADE');

        $handler = self::getContainer()->get(QueryHandler::class);

        self::getContainer()
            ->get(Builder::class)
            ->buildAll($entityName = 'Hello', $attributeName = 'Language', AttributeType::String, $value = 'en');

        $res = $handler->read(new Query());

        $this->assertEquals(1, $res->count);
        $this->assertNotEmpty($res->results[0][EntityIdType::NAME]);
        $this->assertEquals($entityName, $res->results[0]['name']);
        $this->assertCount(1, $res->results[0]['attributes_values']);
        $this->assertEquals($attributeName, $res->results[0]['attributes_values'][0]['name']);
        $this->assertEquals($value, $res->results[0]['attributes_values'][0]['value']);
    }
}

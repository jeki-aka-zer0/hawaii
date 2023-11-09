<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\EAV\Entity\Read;

use App\Application\EAV\Builder;
use App\Application\EAV\Entity\Read\Query;
use App\Application\EAV\Entity\Read\QueryHandler;
use App\Tests\Integration\BaseIntegrationTest;

final class QueryHandlerTest extends BaseIntegrationTest
{
    public function testRead(): void
    {
        self::$connection->executeQuery('TRUNCATE entity CASCADE');

        $handler = self::getContainer()->get(QueryHandler::class);

        self::getContainer()->get(Builder::class)->buildAll('Test entity', 'Test attr', 'Test value');

        $query = new Query();
        $res = $handler->read($query);

        $this->assertEquals(1, $res->count);

//        $this->entities->add(
//            new Entity(
//                $entityId = EntityId::generate(),
//                'Entity A',
//                null,
//            )
//        );
    }
}

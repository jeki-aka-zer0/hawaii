<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\EAV\Entity\Read;

use App\Application\EAV\Entity\Read\Query;
use App\Application\EAV\Entity\Read\QueryHandler;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\EAV\Entity\Repository\EntityRepository;
use App\Domain\Shared\Util\Str;
use App\Tests\Integration\BaseIntegrationTest;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class QueryHandlerTest extends BaseIntegrationTest
{
    public function testRead(): void
    {
        self::$connection->executeQuery('TRUNCATE entity CASCADE');

        /** @var QueryHandler $handler */
        $handler = self::getContainer()->get(QueryHandler::class);

        $query = new Query();
        $res = $handler->read($query);

        $this->assertEquals($res->count, 0);

//        $this->entities->add(
//            new Entity(
//                $entityId = EntityId::generate(),
//                'Entity A',
//                null,
//            )
//        );
    }
}

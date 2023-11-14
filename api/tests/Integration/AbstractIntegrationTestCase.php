<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractIntegrationTestCase extends KernelTestCase
{
    protected static Connection $connection;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();

        /** @var Connection $connection */
        $connection = self::getContainer()->get(Connection::class);
        self::$connection = $connection;
    }


    protected function setUp(): void
    {
        parent::setUp();
        self::$connection->beginTransaction();
    }

    protected function tearDown(): void
    {
        self::$connection->rollBack();
        parent::tearDown();
    }
}

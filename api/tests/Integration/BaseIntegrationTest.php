<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class BaseIntegrationTest extends KernelTestCase
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
        /** @noinspection PhpUnhandledExceptionInspection */
        self::$connection->beginTransaction();
    }

    protected function tearDown(): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        self::$connection->rollBack();
        parent::tearDown();
    }
}

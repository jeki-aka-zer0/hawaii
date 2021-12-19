<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\EAV\Create;

use App\Application\EAV\Create\Command;
use App\Application\EAV\Create\Handler;
use App\Infrastructure\Dummy\EAV\InMemoryEntityRepository;
use App\Infrastructure\Dummy\Flusher;
use App\Tests\Unit\Domain\EAV\Entity\EntityBuilder;
use DomainException;
use JetBrains\PhpStorm\Pure;
use PHPUnit\Framework\TestCase;

final class HandlerTest extends TestCase
{
    private const NAMES_DATA_PROVIDER = [
        'existent name' => ['name' => EntityBuilder::TEST_EXISTENT_NAME],
        'existent name with spaces' => ['name' => ' '.EntityBuilder::TEST_EXISTENT_NAME.' '],
    ];

    public function namesDataProvider(): array
    {
        return self::NAMES_DATA_PROVIDER;
    }

    /**
     * @dataProvider namesDataProvider
     * @param string $name
     * @return void
     */
    public function testHandleShouldFailWhenEntityWithSameAlreadyExists(string $name): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(sprintf('An entity with the name "%s" already exists.', trim($name)));

        $this->getHandler()->handle($this->getCommand());
    }

    private function getHandler(): Handler
    {
        return new Handler(
            new InMemoryEntityRepository([
                (new EntityBuilder())->build(),
            ]),
            new Flusher(),
        );
    }

    #[Pure]
    private function getCommand(): Command
    {
        $command = new Command();
        $command->name = EntityBuilder::TEST_EXISTENT_NAME;
        $command->description = 'Another test description';

        return $command;
    }
}

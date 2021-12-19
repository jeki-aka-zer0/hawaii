<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\EAV\Create;

use App\Application\EAV\Create\Command;
use App\Application\EAV\Create\Handler;
use App\Infrastructure\Dummy\DummyFlusher;
use App\Infrastructure\Dummy\EAV\InMemoryEntityRepository;
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

    private InMemoryEntityRepository $entities;
    private DummyFlusher $flusher;
    private Handler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entities = new InMemoryEntityRepository([
            (new EntityBuilder())->build(),
        ]);
        $this->flusher = new DummyFlusher();
        $this->handler = new Handler($this->entities, $this->flusher);
    }

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
        $command = $this->getCommand(EntityBuilder::TEST_EXISTENT_NAME, 'Another test description');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(sprintf('An entity with the name "%s" already exists.', trim($name)));

        $this->handler->handle($command);
    }

    public function testHandleShouldSuccess(): void
    {
        $name = 'Some name';
        $description = 'Some description';
        $command = $this->getCommand($name, $description);

        $entityId = $this->handler->handle($command);

        self::assertNotEmpty($entityId);
        self::assertTrue($this->entities->hasByName($name));
        self::assertTrue($this->flusher->isFlushed());
    }

    #[Pure]
    private function getCommand(string $name, string $description): Command
    {
        $command = new Command();
        $command->name = $name;
        $command->description = $description;

        return $command;
    }
}

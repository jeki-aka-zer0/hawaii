<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\EAV\Entity\Create;

use App\Application\EAV\Entity\Create\Command;
use App\Application\EAV\Entity\Create\CommandHandler;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Infrastructure\Dummy\DummyFlusher;
use App\Infrastructure\Dummy\EAV\Entity\InMemoryRepository;
use App\Tests\Unit\Domain\EAV\Entity\EntityBuilder;
use DomainException;
use Faker\Factory;
use JetBrains\PhpStorm\Pure;
use PHPUnit\Framework\TestCase;

final class CommandHandlerTest extends TestCase
{
    private InMemoryRepository $entities;
    private DummyFlusher $flusher;
    private CommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entities = new InMemoryRepository([
            (new EntityBuilder())->build(),
        ]);
        $this->flusher = new DummyFlusher();
        $this->handler = new CommandHandler($this->entities, $this->flusher);
    }

    public function namesDataProvider(): array
    {
        return [
            'existent name' => [Entity::FIELD_NAME => EntityBuilder::TEST_EXISTENT_NAME],
            'existent name with spaces' => [Entity::FIELD_NAME => ' ' . EntityBuilder::TEST_EXISTENT_NAME . ' '],
            'existent name with upper case' => [Entity::FIELD_NAME => mb_strtoupper(EntityBuilder::TEST_EXISTENT_NAME)],
        ];
    }

    /**
     * @dataProvider namesDataProvider
     */
    public function testHandleShouldFailWhenEntityWithSameNameAlreadyExists(string $name): void
    {
        $command = $this->getCommand($name, 'Another test description');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(sprintf('Entity with the name "%s" already exists', trim($name)));

        $this->handler->handle($command);
    }

    public function testHandleShouldSuccess(): void
    {
        $name = Factory::create()->name;
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

<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\EAV\Entity\Create;

use App\Application\EAV\Builder;
use App\Application\EAV\Entity\Create\Command;
use App\Application\EAV\Entity\Create\CommandHandler;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Infrastructure\Dummy\DummyFlusher;
use App\Infrastructure\Dummy\EAV\Entity\InMemoryRepository;
use DomainException;
use PHPUnit\Framework\TestCase;

final class CommandHandlerTest extends TestCase
{
    private InMemoryRepository $entities;
    private DummyFlusher $flusher;
    private CommandHandler $handler;
    private static Entity $alreadyExistentEntity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new CommandHandler(
            $this->entities = new InMemoryRepository([self::getAlreadyExistentEntity()]),
            $this->flusher = new DummyFlusher()
        );
    }

    public function namesDataProvider(): array
    {
        return [
            'existent name' => ['name' => self::getAlreadyExistentEntity()->name],
            'existent name with spaces' => ['name' => sprintf(' %s ', self::getAlreadyExistentEntity()->name)],
            'existent name with upper case' => ['name' => mb_strtoupper(self::getAlreadyExistentEntity()->name)],
        ];
    }

    /**
     * @dataProvider namesDataProvider
     */
    public function testHandleShouldFailWhenEntityWithSameNameAlreadyExists(string $name): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(sprintf('Entity with the name "%s" already exists', trim($name)));

        $this->handler->handle(Command::build($name));
    }

    public function testHandleShouldSuccess(): void
    {
        $name = Builder::getRandEntityName(self::getAlreadyExistentEntity()->name);

        $entityId = $this->handler->handle(Command::build($name));

        self::assertNotEmpty($entityId);
        self::assertTrue($this->entities->hasByName($name));
        self::assertTrue($this->flusher->isFlushed());
    }

    private static function getAlreadyExistentEntity(): Entity
    {
        return self::$alreadyExistentEntity ??= Builder::buildEntity();
    }
}

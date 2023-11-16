<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\EAV\Attribute\Create;

use App\Application\EAV\Attribute\Create\Command;
use App\Application\EAV\Attribute\Create\CommandHandler;
use App\Application\EAV\Builder;
use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Domain\Shared\Repository\FieldException;
use App\Infrastructure\Dummy\DummyFlusher;
use App\Infrastructure\Dummy\EAV\Attribute\InMemoryRepository;
use PHPUnit\Framework\TestCase;

final class CommandHandlerTest extends TestCase
{
    private static InMemoryRepository $entities;
    private static DummyFlusher $flusher;
    private static CommandHandler $handler;
    private static Attribute $alreadyExistentAttribute;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$handler = new CommandHandler(
            self::$entities = new InMemoryRepository([self::getAlreadyExistentAttribute()]),
            self::$flusher = new DummyFlusher(),
        );
    }

    public function namesDataProvider(): array
    {
        return [
            'existent name' => ['name' => self::getAlreadyExistentAttribute()->name],
            'existent name with spaces' => [
                'name' => sprintf(' %s ', self::getAlreadyExistentAttribute()->name),
            ],
        ];
    }

    /**
     * @dataProvider namesDataProvider
     */
    public function testHandleShouldFailWhenAttributeWithSameNameAlreadyExists(string $name): void
    {
        $command = $this->getCommand(self::getAlreadyExistentAttribute()->name);

        $this->expectException(FieldException::class);
        $this->expectExceptionMessage(sprintf('Attribute with the name "%s" already exists', trim($name)));

        self::$handler->handle($command);
    }

    public function testHandleShouldCreateAttribute(): void
    {
        $name = Builder::getRandomAttributeName(self::getAlreadyExistentAttribute()->name);
        $command = $this->getCommand($name);

        $attributeId = self::$handler->handle($command);

        self::assertNotEmpty($attributeId);
        self::assertTrue(self::$entities->hasByName($name));
        self::assertTrue(self::$flusher->isFlushed());
    }

    private function getCommand(string $name): Command
    {
        return Command::build($name, AttributeType::String);
    }

    private static function getAlreadyExistentAttribute(): Attribute
    {
        return self::$alreadyExistentAttribute ??= Builder::buildAttribute();
    }
}

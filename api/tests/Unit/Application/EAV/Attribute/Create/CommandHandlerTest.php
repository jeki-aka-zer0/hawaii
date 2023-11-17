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
    private static Attribute $alreadyExistentAttr;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$handler = new CommandHandler(
            self::$entities = new InMemoryRepository([self::getAlreadyExistentAttr()]),
            self::$flusher = new DummyFlusher(),
        );
    }

    public function namesDataProvider(): array
    {
        return [
            'existent name' => ['name' => self::getAlreadyExistentAttr()->name],
            'existent name with spaces' => [
                'name' => sprintf(' %s ', self::getAlreadyExistentAttr()->name),
            ],
        ];
    }

    /**
     * @dataProvider namesDataProvider
     */
    public function testHandleShouldFailWhenAttrWithSameNameAlreadyExists(string $name): void
    {
        $cmd = $this->getCommand(self::getAlreadyExistentAttr()->name);

        $this->expectException(FieldException::class);
        $this->expectExceptionMessage(sprintf('Attribute with the name "%s" already exists', trim($name)));

        self::$handler->handle($cmd);
    }

    public function testHandleShouldCreateAttr(): void
    {
        $name = Builder::getRandomAttrName(self::getAlreadyExistentAttr()->name);
        $cmd = $this->getCommand($name);

        $attrId = self::$handler->handle($cmd);

        self::assertNotEmpty($attrId);
        self::assertTrue(self::$entities->hasByName($name));
        self::assertTrue(self::$flusher->isFlushed());
    }

    private function getCommand(string $name): Command
    {
        return Command::build($name, AttributeType::String);
    }

    private static function getAlreadyExistentAttr(): Attribute
    {
        return self::$alreadyExistentAttr ??= Builder::buildAttr();
    }
}

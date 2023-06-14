<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\EAV\Attribute\Create;

use App\Application\EAV\Attribute\Create\Command;
use App\Application\EAV\Attribute\Create\CommandHandler;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Domain\Shared\Repository\FieldException;
use App\Infrastructure\Dummy\DummyFlusher;
use App\Infrastructure\Dummy\EAV\Attribute\InMemoryRepository;
use App\Tests\Unit\Domain\EAV\Attribute\AttributeBuilder;
use DomainException;
use Faker\Factory;
use JetBrains\PhpStorm\Pure;
use PHPUnit\Framework\TestCase;

final class CommandHandlerTest extends TestCase
{
    private const NAMES_DATA_PROVIDER = [
        'existent name' => ['name' => AttributeBuilder::TEST_EXISTENT_NAME],
        'existent name with spaces' => ['name' => ' '.AttributeBuilder::TEST_EXISTENT_NAME.' '],
    ];

    private InMemoryRepository $entities;
    private DummyFlusher $flusher;
    private CommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entities = new InMemoryRepository([
            (new AttributeBuilder())->build(),
        ]);
        $this->flusher = new DummyFlusher();
        $this->handler = new CommandHandler($this->entities, $this->flusher);
    }

    public function namesDataProvider(): array
    {
        return self::NAMES_DATA_PROVIDER;
    }

    /**
     * @dataProvider namesDataProvider
     */
    public function testHandleShouldFailWhenAttributeWithSameNameAlreadyExists(string $name): void
    {
        $command = $this->getCommand(AttributeBuilder::TEST_EXISTENT_NAME);

        $this->expectException(FieldException::class);
        $this->expectExceptionMessage(sprintf('An attribute with the name "%s" already exists.', trim($name)));

        $this->handler->handle($command);
    }

    public function testHandleShouldSuccess(): void
    {
        $name = Factory::create()->name;
        $command = $this->getCommand($name);

        $attributeId = $this->handler->handle($command);

        self::assertNotEmpty($attributeId);
        self::assertTrue($this->entities->hasByName($name));
        self::assertTrue($this->flusher->isFlushed());
    }

    #[Pure]
    private function getCommand(string $name): Command
    {
        $command = new Command();
        $command->name = $name;
        $command->type = AttributeType::String->value;

        return $command;
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\EAV\Value\Create;

use App\Application\EAV\Value\Upsert\Command;
use App\Application\EAV\Value\Upsert\CommandHandler;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Infrastructure\Dummy\DummyFlusher;
use App\Infrastructure\Dummy\EAV\Attribute\InMemoryRepository as Attributes;
use App\Infrastructure\Dummy\EAV\Entity\InMemoryRepository as Entites;
use App\Infrastructure\Dummy\EAV\Value\InMemoryRepository;
use App\Tests\Unit\Domain\EAV\Attribute\AttributeBuilder;
use App\Tests\Unit\Domain\EAV\Entity\EntityBuilder;
use App\Tests\Unit\Domain\EAV\Value\ValueBuilder;
use PHPUnit\Framework\TestCase;

final class CommandHandlerTest extends TestCase
{
    private const STRING_VALUE = 'Some unique value imagined only for this current test';

    private const VALUES_DATA_PROVIDER = [
        'int' => ['value' => 1000],
        'string' => ['value' => self::STRING_VALUE],
    ];

    public function valuesDataProvider(): array
    {
        return self::VALUES_DATA_PROVIDER;
    }

    /**
     * @dataProvider valuesDataProvider
     */
    public function testHandleShouldCreate(string|int $value): void
    {
        [$handler, $flusher, $command, $values] = $this->buildHandlerAndOtherEntities(false, $value);

        $valueId = $handler->handle($command);

        self::assertTrue($flusher->isFlushed());
        self::assertEquals($command->value, $values->get($valueId)->value);
    }

    public function testHandleShouldUpdate(): void
    {
        [$handler, $flusher, $command, $values, $value] = $this->buildHandlerAndOtherEntities(true);

        $valueId = $handler->handle($command);

        self::assertTrue($flusher->isFlushed());
        self::assertEquals($command->value, $values->get($valueId)->value);
        self::assertSame($value, $values->get($valueId));
    }

    private function buildHandlerAndOtherEntities(bool $persistValue, string|int $value = null): array
    {
        $entityId = EntityId::generate();
        $attributeId = AttributeId::generate();

        $command = new Command();
        $command->entityId = $entityId->getValue();
        $command->attributeId = $attributeId->getValue();
        $command->value = $value ?? self::STRING_VALUE;

        $entity = (new EntityBuilder())->build($entityId);
        $attribute = (new AttributeBuilder())->build($attributeId);
        $value = (new ValueBuilder())->build($entity, $attribute);

        return [
            new CommandHandler(
                $values = new InMemoryRepository($persistValue ? [$value] : []),
                new Attributes([$attribute]),
                new Entites([$entity]),
                $flusher = new DummyFlusher()
            ),
            $flusher,
            $command,
            $values,
            $value,
        ];
    }
}

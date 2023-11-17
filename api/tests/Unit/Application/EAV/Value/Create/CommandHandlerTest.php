<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\EAV\Value\Create;

use App\Application\EAV\Builder;
use App\Application\EAV\Value\Upsert\Command;
use App\Application\EAV\Value\Upsert\CommandHandler;
use App\Domain\EAV\Attribute\Entity\AttributeId;
use App\Domain\EAV\Entity\Entity\EntityId;
use App\Domain\EAV\Value\Entity\Value;
use App\Domain\EAV\Value\Repository\ValueRepository;
use App\Domain\Flusher;
use App\Infrastructure\Dummy\DummyFlusher;
use App\Infrastructure\Dummy\EAV\Attribute\InMemoryRepository as Attributes;
use App\Infrastructure\Dummy\EAV\Entity\InMemoryRepository as Entities;
use App\Infrastructure\Dummy\EAV\Value\InMemoryRepository;
use App\Infrastructure\UI\Web\Request\CommandInterface;
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
        [$handler, $flusher, $cmd, $values] = $this->buildHandlerAndOtherEntities(false, $value);

        $valueId = $handler->handle($cmd);

        self::assertTrue($flusher->isFlushed());
        self::assertEquals($cmd->value, $values->get($valueId)->value);
    }

    public function testHandleShouldUpdate(): void
    {
        [$handler, $flusher, $cmd, $values, $value] = $this->buildHandlerAndOtherEntities(true);

        $valueId = $handler->handle($cmd);

        self::assertTrue($flusher->isFlushed());
        self::assertEquals($cmd->value, $values->get($valueId)->value);
        self::assertSame($value, $values->get($valueId));
    }

    /**
     * @return array{CommandHandler, Flusher|DummyFlusher, CommandInterface|Command, ValueRepository|InMemoryRepository, Value}
     */
    private function buildHandlerAndOtherEntities(bool $persistValue, string|int $value = null): array
    {
        $entityId = EntityId::generate();
        $attrId = AttributeId::generate();

        $cmd = Command::build($entityId, $attrId, $value ?? self::STRING_VALUE);

        $entity = Builder::buildEntity($entityId);
        $attribute = Builder::buildAttr($attrId);
        $value = Builder::buildValue($entity, $attribute);

        return [
            new CommandHandler(
                $values = new InMemoryRepository($persistValue ? [$value] : []),
                new Attributes([$attribute]),
                new Entities([$entity]),
                $flusher = new DummyFlusher()
            ),
            $flusher,
            $cmd,
            $values,
            $value,
        ];
    }
}

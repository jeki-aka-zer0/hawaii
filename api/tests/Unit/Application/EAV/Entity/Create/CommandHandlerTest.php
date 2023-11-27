<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\EAV\Entity\Create;

use App\Application\EAV\Builder;
use App\Application\EAV\Entity\Create\Command;
use App\Application\EAV\Entity\Create\CommandHandler;
use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Value\Entity\Value;
use App\Infrastructure\Dummy\DummyFlusher;
use App\Infrastructure\Dummy\EAV\Attribute\InMemoryRepository as AttrsRepo;
use App\Infrastructure\Dummy\EAV\Entity\InMemoryRepository as EntitiesRepo;
use App\Infrastructure\Dummy\EAV\Value\InMemoryRepository as ValRepo;
use DomainException;
use PHPUnit\Framework\TestCase;

final class CommandHandlerTest extends TestCase
{
    private EntitiesRepo $entities;
    private DummyFlusher $flusher;
    private CommandHandler $handler;
    private static Entity $alreadyExistentEntity;
    private AttrsRepo $attrs;
    private ValRepo $val;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new CommandHandler(
            $this->entities = new EntitiesRepo([self::getAlreadyExistentEntity()]),
            $this->attrs = new AttrsRepo([]),
            $this->val = new ValRepo([]),
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
        $cmd = Command::build($name = self::getNonExistentEntityName());

        $this->handler->handle($cmd);

        $this
            ->assertEntityExists($name)
            ->assertFlushed();
    }

    public function testHandleShouldCreateNewAttrsVal(): void
    {
        $this->handler->handle(
            Command::build($entityName = self::getNonExistentEntityName(), attrsVal: [
                [
                    Attribute::FIELD_NAME => $attrName = Builder::getRandAttrName(),
                    Value::FIELD_VALUE => $val = Builder::getRandStrValue(),
                ],
            ])
        );

        $this
            ->assertEntityExists($entityName)
            ->assertFlushed();
        self::assertTrue($this->attrs->hasByName($attrName));
        self::assertTrue($this->val->hasByValEntityAndAttrNames($val, $entityName, $attrName));
    }

    private static function getAlreadyExistentEntity(): Entity
    {
        return self::$alreadyExistentEntity ??= Builder::buildEntity();
    }

    private static function getNonExistentEntityName(): string
    {
        return Builder::getRandEntityName(self::getAlreadyExistentEntity()->name);
    }

    private function assertEntityExists(string $name): self
    {
        self::assertTrue($this->entities->hasByName($name));

        return $this;
    }

    private function assertFlushed(): self
    {
        self::assertTrue($this->flusher->isFlushed());

        return $this;
    }
}

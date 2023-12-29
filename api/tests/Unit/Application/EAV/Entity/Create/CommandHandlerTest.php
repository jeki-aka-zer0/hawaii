<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\EAV\Entity\Create;

use App\Application\EAV\Builder;
use App\Application\EAV\Entity\Create\Command;
use App\Application\EAV\Entity\Create\CommandHandler;
use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Value\Entity\Value;
use App\Domain\Shared\Repository\FieldException;
use App\Infrastructure\Doctrine\EAV\Attribute\AttributeIdType;
use App\Infrastructure\Dummy\DummyFlusher;
use App\Infrastructure\Dummy\EAV\Attribute\InMemoryRepository as AttrsRepo;
use App\Infrastructure\Dummy\EAV\Entity\InMemoryRepository as EntitiesRepo;
use App\Infrastructure\Dummy\EAV\Value\InMemoryRepository as ValRepo;
use DomainException;
use PHPUnit\Framework\TestCase;

final class CommandHandlerTest extends TestCase
{
    private EntitiesRepo $entities;
    private AttrsRepo $attrs;
    private ValRepo $val;
    private DummyFlusher $flusher;
    private CommandHandler $SUT;
    private static Entity $alreadyExistentEntity;
    private static Attribute $alreadyExistentAttr;

    protected function setUp(): void
    {
        parent::setUp();

        $this->SUT = new CommandHandler(
            $this->entities = new EntitiesRepo([self::getAlreadyExistentEntity()]),
            $this->attrs = new AttrsRepo([self::getAlreadyExistentAttr()]),
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
        $this->expectExceptionWithMsg(
            DomainException::class,
            sprintf('Entity with the name "%s" already exists', trim($name))
        );

        $this->SUT->handle(Command::build($name));
    }

    public function testHandleShouldSuccess(): void
    {
        $cmd = Command::build($name = self::getNonExistentEntityName());

        $this->SUT->handle($cmd);

        $this
            ->assertEntityExists($name)
            ->assertFlushed();
    }

    public function testHandleShouldCreateNewAttrsVal(): void
    {
        $cmd = Command::build($entityName = self::getNonExistentEntityName(), attrsVal: [
            'attribute should be created' => [
                Attribute::FIELD_NAME => $newAttrName = Builder::getRandAttrName([self::getAlreadyExistentAttr()->name]),
                Value::FIELD_VALUE => $valOfNewAttr = Builder::getRandVal(),
            ],
            'already existent attribute should be found' => [
                Attribute::FIELD_NAME => $existentAttrName = self::getAlreadyExistentAttr()->name,
                Value::FIELD_VALUE => $valForExistentAttr = Builder::getRandVal(self::getAlreadyExistentAttr()),
            ],
        ]);

        $this->SUT->handle($cmd);

        $this
            ->assertEntityExists($entityName)
            ->assertFlushed();
        self::assertTrue($this->attrs->hasByName($newAttrName));
        self::assertTrue($this->attrs->hasByName($existentAttrName));
        self::assertTrue($this->val->hasByValEntityAndAttrNames($valOfNewAttr, $entityName, $newAttrName));
        self::assertTrue($this->val->hasByValEntityAndAttrNames($valForExistentAttr, $entityName, $existentAttrName));
    }

    public function testHandleShouldFailWhenAttrIdIsNotValidUuid(): void
    {
        $cmd = Command::build(self::getNonExistentEntityName(), attrsVal: [
            [
                AttributeIdType::FIELD_ATTR_ID => 'non valid uuid',
                Attribute::FIELD_NAME => Builder::getRandAttrName([self::getAlreadyExistentAttr()->name]),
                Value::FIELD_VALUE => Builder::getRandStrVal(),
            ],
        ]);
        $this->expectExceptionWithMsg(FieldException::class, 'Attribute id is invalid identifier');

        $this->SUT->handle($cmd);
    }

    private static function getAlreadyExistentEntity(): Entity
    {
        return self::$alreadyExistentEntity ??= Builder::buildEntity();
    }

    private static function getAlreadyExistentAttr(): Attribute
    {
        return self::$alreadyExistentAttr ??= Builder::buildAttr();
    }

    private static function getNonExistentEntityName(): string
    {
        return Builder::getRandEntityName([self::getAlreadyExistentEntity()->name]);
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

    public function expectExceptionWithMsg(string $class, string $msg): void
    {
        $this->expectException($class);
        $this->expectExceptionMessage($msg);
    }
}

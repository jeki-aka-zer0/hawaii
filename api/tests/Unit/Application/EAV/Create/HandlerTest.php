<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\EAV\Create;

use App\Application\EAV\Create\Command;
use App\Application\EAV\Create\Handler;
use App\Domain\EAV\Entity\Entity;
use App\Domain\EAV\Entity\EntityId;
use App\Infrastructure\Dummy\EAV\EntityRepository;
use App\Infrastructure\Dummy\Flusher;
use DomainException;
use JetBrains\PhpStorm\Pure;
use PHPUnit\Framework\TestCase;

final class HandlerTest extends TestCase
{
    private const TEST_EXISTENT_NAME = 'Test name';

    public function namesDataProvider(): array
    {
        return [
            'existent name' => ['name' => self::TEST_EXISTENT_NAME],
            'existent name with spaces' => ['name' => ' '.self::TEST_EXISTENT_NAME.' '],
        ];
    }

    /**
     * @dataProvider namesDataProvider
     * @param string $name
     * @return void
     */
    public function testHandleShouldFailWhenEntityWithSameAlreadyExists(string $name): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(sprintf('An entity with the name "%s" already exists.', trim($name)));

        $this->getHandler()->handle($this->getCommand());
    }

    private function getHandler(): Handler
    {
        return new Handler(
            new EntityRepository([new Entity(EntityId::generate(), self::TEST_EXISTENT_NAME, 'Test description')]),
            new Flusher()
        );
    }

    #[Pure]
    private function getCommand(): Command
    {
        $command = new Command();
        $command->name = self::TEST_EXISTENT_NAME;
        $command->description = 'Another test description';

        return $command;
    }
}

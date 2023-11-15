<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\CLI\EAV;

use App\Application\EAV\Builder;
use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Value\Entity\Value;
use App\Domain\Shared\Repository\FieldException;
use App\Domain\Shared\Util\Str;
use App\Infrastructure\UI\CLI\AbstractCommand;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

final class PopulateCommand extends AbstractCommand
{
    public function __construct(private readonly Builder $builder, private readonly Connection $connection)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('eav:populate')
            ->setDescription(('Truncate and re-create a batch of random entities with attributes and values'));
    }

    /**
     * @throws Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $this->connection->executeQuery(sprintf('TRUNCATE %s, %s, %s CASCADE', Entity::NAME, Attribute::NAME, Value::NAME));

        // create attributes
        $attributeNameToEntityMap = [];
        try {
            foreach (Builder::ATTRIBUTE_NAME_TO_TYPE_MAP as $attrName => $attrType) {
                $attributeNameToEntityMap[$attrName] = $this->builder->buildAttribute($attrName, $attrType);
            }
        } catch (FieldException) {
            $output->writeln(sprintf("<question>%s '%s' already exist</question>", (new Str(Attribute::NAME))->humanize(), $attrName));
        }

        // create entities
        $maxAttributesIndex = count($attributeNameToEntityMap);
        foreach (Builder::ENTITY_NAME_TO_DESC_MAP as $entityName => $entityDesc) {
            $entityId = $this->builder->buildEntity($entityName, $entityDesc);
            // create values
            $attributesNumber = rand(0, $maxAttributesIndex);
            for ($i = 0; $i < $attributesNumber; $i++) {
                $attributeName = array_rand($attributeNameToEntityMap);
                $this->builder->buildValue(
                    $entityId,
                    $attributeNameToEntityMap[$attributeName],
                    match (Builder::ATTRIBUTE_NAME_TO_TYPE_MAP[$attributeName]) {
                        AttributeType::String => Builder::getRandomStrValue(),
                        AttributeType::Int => Builder::getRandomIntValue(),
                    }
                );
            }
        }

        return $this->success($output);
    }
}

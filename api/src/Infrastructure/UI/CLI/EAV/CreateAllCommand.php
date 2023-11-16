<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\CLI\EAV;

use App\Application\EAV\Builder;
use App\Domain\EAV\Attribute\Entity\Attribute;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Domain\EAV\Entity\Entity\Entity;
use App\Domain\EAV\Value\Entity\Value;
use App\Infrastructure\Doctrine\EAV\Attribute\AttributeTypeType;
use App\Infrastructure\UI\CLI\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateAllCommand extends AbstractCommand
{
    public function __construct(private readonly Builder $builder)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('eav:create-all')
            ->setDescription('Create entity, attribute and value')
            ->addArgument(Entity::NAME, InputArgument::REQUIRED, 'Entity name')
            ->addArgument(Attribute::NAME, InputArgument::REQUIRED, 'Attribute name')
            ->addArgument(AttributeTypeType::NAME, InputArgument::REQUIRED, 'Attribute type (string or int)')
            ->addArgument(Value::NAME, InputArgument::REQUIRED, 'Value');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        $this->builder->createAll(
            $input->getArgument(Entity::NAME),
            $input->getArgument(Attribute::NAME),
            AttributeType::from($input->getArgument(AttributeTypeType::NAME)),
            $input->getArgument(Value::NAME),
        );

        return $this->success($output);
    }
}

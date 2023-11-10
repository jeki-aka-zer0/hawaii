<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\CLI\EAV;

use App\Application\EAV\Builder;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateAllCommand extends Command
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
            ->addArgument('entity', InputArgument::REQUIRED, 'Entity name')
            ->addArgument('attribute', InputArgument::REQUIRED, 'Attribute name')
            ->addArgument('attribute_type', InputArgument::REQUIRED, 'Attribute type (string or int)')
            ->addArgument('value', InputArgument::REQUIRED, 'Value');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Start</info>');

        $this->builder->buildAll(
            $input->getArgument('entity'),
            $input->getArgument('attribute'),
            AttributeType::from($input->getArgument('attribute_type')),
            $input->getArgument('value'),
        );

        $output->writeln('<info>Done!</info>');

        return self::SUCCESS;
    }
}

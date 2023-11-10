<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\CLI\EAV;

use App\Application\EAV\Builder;
use App\Domain\EAV\Attribute\Entity\AttributeType;
use App\Domain\Shared\Repository\FieldException;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PopulateCommand extends Command
{
    private const ENTITIES = [
        'Elasticsearch' => 'Is a search and analytics engine.',
        'Logstash' => 'Is a server‑side data processing pipeline that ingests data from multiple sources simultaneously, transforms it, and then sends it to a "stash" like Elasticsearch.',
        'Kibana' => 'Lets users visualize data with charts and graphs in Elasticsearch.',
        'Beat' => 'Is a family of lightweight, single-purpose data shippers. After adding this tool ELK was renamed to Elastic stack.',
        'Spoofing' => 'In the context of information security, and especially network security, a spoofing attack is a situation in which a person or program successfully identifies as another by falsifying data, to gain an illegitimate advantage.',
        'CGI' => 'A protocol for calling external software via a Web server to deliver dynamic content.',
        'FastCGI' => 'is a binary protocol for interfacing interactive programs with a web server. It is a variation on the earlier Common Gateway Interface (CGI). FastCGI\'s main aim is to reduce the overhead related to interfacing between web server and CGI programs, allowing a server to handle more web page requests per unit of time.',
        'Graceful degradation' => 'Is the ability of a computer, machine, electronic system or network to maintain limited functionality even when a large portion of it has been destroyed or rendered inoperative.',
    ];

    private const ATTRIBUTES = [
        'Keyword' => AttributeType::String,
        'Category' => AttributeType::String,
        'Importance' => AttributeType::Int,
        'Popularity' => AttributeType::Int,
        'Lang' => AttributeType::String,
    ];

    private const VALUES = [
        'Repeat',
        'TODO',
        'Programming',
        'Interesting fact',
        'Education',
        'ELK',
        'CGI',
        'Algorithm',
        'EN',
        'FR',
        'RU',
        'UA',
    ];

    public function __construct(private readonly Builder $builder, private Connection $connection)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('eav:populate')
            ->setDescription('Create certain amount of random entities')
            ->addArgument('number', InputArgument::REQUIRED, 'Number of entities')
            ->addArgument('trunc', InputArgument::OPTIONAL, 'Truncate all the EAV before start');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Start</info>');

        if ($input->getArgument('trunc')) {
            $output->writeln('<question>EAV truncated</question>');

            $this->connection->executeQuery('TRUNCATE entity, attribute, value CASCADE');
        }

        $entityNames = array_keys(self::ENTITIES);

        $attributes = [];
        try {
            foreach (self::ATTRIBUTES as $name => $type) {
                $attributes[$name] = $this->builder->buildAttribute($name, $type);
            }
        } catch (FieldException) {
            $output->writeln("<question>Attribute '{$name}' already exist</question>");
        }
        $maxAttributesIndex = count($attributes) - 1;

        $entitiesNumber = (int)$input->getArgument('number');
        if ($entitiesNumber < 0) {
            return self::SUCCESS;
        }
        if ($entitiesNumber > count(self::ENTITIES)) {
            $entitiesNumber = count(self::ENTITIES);
        }
        for ($i = 0; $i < $entitiesNumber; $i++) {
            $entityName = $entityNames[$i];
            $entityId = $this->builder->buildEntity($entityName, self::ENTITIES[$entityName],);

            $attributesNumber = rand(0, $maxAttributesIndex);
            for ($j = 0; $j < $attributesNumber; $j++) {
                $attributeName = array_rand($attributes);
                $attributeId = $attributes[$attributeName];

                $this->builder->buildValue($entityId, $attributeId, match (self::ATTRIBUTES[$attributeName]) {
                    AttributeType::String => self::VALUES[array_rand(self::VALUES)],
                    AttributeType::Int => rand(1, 10),
                });
            }
        }

        $output->writeln('<info>Done!</info>');

        return self::SUCCESS;
    }
}

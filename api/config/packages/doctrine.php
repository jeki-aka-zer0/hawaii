<?php

declare(strict_types=1);

use App\Infrastructure\Doctrine\EAV\Attribute\AttributeIdType;
use App\Infrastructure\Doctrine\EAV\Attribute\AttributeTypeType;
use App\Infrastructure\Doctrine\EAV\Entity\EntityIdType;
use App\Infrastructure\Doctrine\EAV\Value\ValueIdType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'url' => '%env(resolve:DATABASE_URL)%',
            'charset' => 'UTF8',
            'server_version' => 14.1,
            'types' => [
                'entity_id' => EntityIdType::class,
                'attribute_id' => AttributeIdType::class,
                'attribute_type' => AttributeTypeType::class,
                'value_id' => ValueIdType::class,
            ],
        ],
        'orm' => [
            'auto_generate_proxy_classes' => true,
            'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
            'auto_mapping' => true,
            'report_fields_where_declared' => true,
            'enable_lazy_ghost_objects' => true,
            'mappings' => [
                /**
                 * Describe each entity folder separately
                 * if and when the doctrine:migrations:diff console command starts working slowly
                 */
                'Domain' => [
                    'type' => 'attribute',
                    'dir' => '%kernel.project_dir%/src/Domain',
                    'prefix' => 'App\Domain',
                ],
            ],
        ],
    ]);
};

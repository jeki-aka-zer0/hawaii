<?php

declare(strict_types=1);

use App\Domain\EAV\Entity\Repository\EntityRepository;
use App\Domain\Flusher;
use App\Infrastructure\Doctrine\DbFlusher;
use App\Infrastructure\Doctrine\EAV\Entity\DbEntityRepository;
use App\Infrastructure\UI\Web\Request\BodyResolver;
use App\Infrastructure\UI\Web\Request\QueryResolver;
use App\Infrastructure\UI\Web\Response\ExceptionListener;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind(EntityRepository::class, service(DbEntityRepository::class))
        ->bind(Flusher::class, service(DbFlusher::class));

    $services->load('App\\', __DIR__.'/../src/')
        ->exclude(
            [
                __DIR__.'/../src/Kernel.php',
                __DIR__.'/../src/Tests/',
            ]
        );

    $services->set(BodyResolver::class)
        ->tag('controller.argument_value_resolver', ['priority' => 50]);

    $services->set(QueryResolver::class)
        ->tag('controller.argument_value_resolver', ['priority' => 50]);

    $services->set(ExceptionListener::class)
        ->tag('kernel.event_listener', ['event' => 'kernel.exception']);
};

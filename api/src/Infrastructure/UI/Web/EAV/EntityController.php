<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\EAV;

use App\Application\EAV\Builder;
use App\Application\EAV\Entity\Create\Command;
use App\Application\EAV\Entity\Create\CommandHandler;
use App\Application\EAV\Entity\Read\Query;
use App\Application\EAV\Entity\Read\QueryHandler;
use App\Application\EAV\Entity\Read\QueryOne;
use App\Infrastructure\UI\Web\Response\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class EntityController extends AbstractController
{
    private const EAV_ENTITIES_READ = 'eav_entities_read';

    public function __construct(private readonly UrlGeneratorInterface $router, private Builder $builder /** todo fix (create a CLI command): temporary fix for autowiring in tests */)
    {
    }

    #[Route('/eav/entity', name: 'eav_entity_create', methods: ['POST'])]
    public function create(Command $command, CommandHandler $handler): Response
    {
        return new JsonResponse(
            [
                'entity_id' => $handler->handle($command)->getValue(),
            ],
            status: 201
        );
    }

    #[Route('/eav/entity', name: self::EAV_ENTITIES_READ, methods: ['GET', 'HEAD'])]
    public function read(Query $query, QueryHandler $handler): Response
    {
        return new JsonResponse(
            (new Paginator(
                $query,
                self::EAV_ENTITIES_READ,
                $this->router,
                $handler->read($query),
            ))
                ->build()
                ->toArray()
        );
    }

    #[Route('/eav/entity/{entityId}', name: 'eav_entity_read', methods: ['GET', 'HEAD'])]
    public function readOne(QueryOne $query, QueryHandler $handler): Response
    {
        return new JsonResponse($handler->oneOrFail($query->getEntityId()));
    }
}

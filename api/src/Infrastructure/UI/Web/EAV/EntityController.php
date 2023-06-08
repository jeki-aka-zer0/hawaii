<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\EAV;

use App\Application\EAV\Entity\Create\Command;
use App\Application\EAV\Entity\Create\CommandHandler;
use App\Application\EAV\Entity\Read\Query;
use App\Application\EAV\Entity\Read\QueryHandler;
use App\Infrastructure\UI\Web\Response\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class EntityController extends AbstractController
{
    private const EAV_ENTITY_LIST = 'eav_entity_list';

    public function __construct(private readonly UrlGeneratorInterface $router)
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

    #[Route('/eav/entity', name: self::EAV_ENTITY_LIST, methods: ['GET', 'HEAD'])]
    public function read(Query $query, QueryHandler $handler): Response
    {
        return new JsonResponse(
            (new Paginator(
                $query,
                self::EAV_ENTITY_LIST,
                $this->router,
                $handler->fetch($query),
            ))
                ->build()
                ->toArray()
        );
    }
}

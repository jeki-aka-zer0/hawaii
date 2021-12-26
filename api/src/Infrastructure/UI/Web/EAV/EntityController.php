<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\EAV;

use App\Application\EAV\Create\Command;
use App\Application\EAV\Create\CommandHandler;
use App\Application\EAV\Read\Query;
use App\Application\EAV\Read\QueryHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class EntityController extends AbstractController
{
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

    #[Route('/eav/entity', name: 'eav_entity_list', methods: ['GET', 'HEAD'])]
    public function read(Query $query, QueryHandler $handler): Response
    {
        return new JsonResponse(
            [
                'entities' => $handler->fetch($query),
            ]
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\EAV;

use App\Application\EAV\Create\Command;
use App\Application\EAV\Create\Handler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class EntityController extends AbstractController
{
    #[Route('/eav/entity', name: 'eav_entity_create', methods: ['POST'], format: 'json')]
    public function create(Command $command, Handler $handler): Response
    {
        $entityId = $handler->handle($command);

        return new JsonResponse(['entity_id' => $entityId], status: 201);
    }

    #[Route('/eav/entity', name: 'eav_entity_list', methods: ['GET', 'HEAD'], format: 'json')]
    public function list(): Response
    {
        return new Response('{"test": "Coming soon"}');
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\EAV;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class EntityController extends AbstractController
{
    #[Route('/eav/entity', name: 'eav_entity_create', methods: ['POST'], format: 'json')]
    public function create(CreateEntityCommand $createEntityCommand): Response
    {
        return new Response('{"test": "Coming soon"}');
    }

    #[Route('/eav/entity', name: 'eav_entity_list', methods: ['GET', 'HEAD'], format: 'json')]
    public function list(): Response
    {
        return new Response('{"test": "Coming soon"}');
    }
}

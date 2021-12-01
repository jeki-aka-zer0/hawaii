<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\EAV;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class EntityController extends AbstractController
{
    #[Route('/eav/entity', name: 'eav_entity_list', methods: ['GET', 'HEAD'])]
    public function list(): Response
    {
        return new Response('Coming soon');
    }
}

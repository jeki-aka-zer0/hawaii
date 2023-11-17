<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\EAV;

use App\Application\EAV\Attribute\Create\Command;
use App\Application\EAV\Attribute\Create\CommandHandler;
use App\Application\EAV\Attribute\Read\Query;
use App\Application\EAV\Attribute\Read\QueryHandler;
use App\Infrastructure\Doctrine\EAV\Attribute\AttributeIdType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AttributeController extends AbstractController
{
    #[Route('/eav/attribute', name: 'eav_attribute_create', methods: ['POST'])]
    public function create(Command $cmd, CommandHandler $handler): Response
    {
        return new JsonResponse(
            [
                AttributeIdType::FIELD_ATTR_ID => $handler->handle($cmd)->getValue(),
            ],
            status: 201
        );
    }

    #[Route('/eav/attribute', name: 'eav_attribute_list', methods: ['GET', 'HEAD'])]
    public function read(Query $query, QueryHandler $handler): Response
    {
        return new JsonResponse(
            [
                'attributes' => $handler->fetch($query),
            ]
        );
    }
}

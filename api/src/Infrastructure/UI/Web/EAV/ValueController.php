<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\EAV;

use App\Application\EAV\Value\Upsert\Command;
use App\Application\EAV\Value\Upsert\CommandHandler;
use App\Infrastructure\Doctrine\EAV\Value\ValueIdType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ValueController extends AbstractController
{
    #[Route('/eav/value', name: 'eav_value_upsert', methods: ['POST'])]
    public function upsert(Command $cmd, CommandHandler $handler): Response
    {
        return new JsonResponse(
            [
                ValueIdType::FIELD_VALUE_ID => $handler->handle($cmd)->getVal(),
            ],
            status: 201
        );
    }
}

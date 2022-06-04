<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\EAV;

use App\Application\EAV\Value\Upsert\Command;
use App\Application\EAV\Value\Upsert\CommandHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ValueController extends AbstractController
{
    #[Route('/eav/value', name: 'eav_value_upsert', methods: ['POST'])]
    public function upsert(Command $command, CommandHandler $handler): Response
    {
        return new JsonResponse(
            [
                'value_id' => $handler->handle($command)->getValue(),
            ],
            status: 201
        );
    }
}

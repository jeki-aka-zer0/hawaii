<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractErrorJsonResponse extends JsonResponse
{
    protected static function buildErrorResponse(array $errors, int $status): static
    {
        return new static(data: ['errors' => $errors], status: $status);
    }
}

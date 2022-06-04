<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Response;

use App\Domain\Shared\Repository\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class HttpErrorJsonResponse extends AbstractErrorJsonResponse
{
    public static function createError(HttpExceptionInterface $exception): self
    {
        return parent::buildErrorResponse([$exception->getMessage()], $exception->getStatusCode());
    }

    public static function createNotFoundError(EntityNotFoundException $exception): self
    {
        return parent::buildErrorResponse([$exception->getMessage()], Response::HTTP_NOT_FOUND);
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Response;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class HttpErrorJsonResponse extends AbstractErrorJsonResponse
{
    public static function createError(HttpExceptionInterface $exception): self
    {
        return parent::buildErrorResponse([$exception->getMessage()], $exception->getStatusCode());
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Response;

use DomainException;
use Symfony\Component\HttpFoundation\Response;

final class DomainErrorJsonResponse extends AbstractErrorJsonResponse
{
    public static function createError(DomainException $exception): AbstractErrorJsonResponse
    {
        return parent::buildErrorResponse([$exception->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

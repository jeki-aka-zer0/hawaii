<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Response;

use Symfony\Component\HttpFoundation\Response;

final class UnknownErrorJsonResponse extends AbstractErrorJsonResponse
{
    public static function createError(): self
    {
        return parent::buildErrorResponse(['Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

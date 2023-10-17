<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Response;

use App\Domain\Shared\Repository\FieldException;
use DomainException;
use Symfony\Component\HttpFoundation\Response;

final class DomainErrorJsonResponse extends AbstractErrorJsonResponse
{
    public static function createError(DomainException $exception): AbstractErrorJsonResponse
    {
        return self::buildErrorResponse([$exception->getMessage()]);
    }

    public static function createFieldError(FieldException $exception): AbstractErrorJsonResponse
    {
        return self::buildErrorResponse([$exception->getField() => [$exception->getMessage()]]);
    }

    protected static function buildErrorResponse(array $errors, int $status = Response::HTTP_UNPROCESSABLE_ENTITY): static
    {
        return parent::buildErrorResponse($errors, $status);
    }
}

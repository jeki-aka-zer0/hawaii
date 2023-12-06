<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Response;

use App\Infrastructure\UI\Web\Request\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

final class ValidationErrorJsonResponse extends AbstractErrorJsonResponse
{
    public static function createError(ValidationException $exception): AbstractErrorJsonResponse
    {
        return parent::buildErrorResponse(self::violationsToArray($exception), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private static function violationsToArray(ValidationException $exception): array
    {
        $errors = [];
        $camelCaseToSnake = new CamelCaseToSnakeCaseNameConverter();
        foreach ($exception->getViolations() as $violation) {
            $errors[($camelCaseToSnake)->normalize($violation->getPropertyPath())] = array_merge(
                $errors[$violation->getPropertyPath()] ?? [],
                [$violation->getMessage()],
            );
        }

        return $errors;
    }
}

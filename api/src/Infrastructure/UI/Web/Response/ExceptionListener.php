<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Response;

use App\Infrastructure\UI\Web\Request\ValidationException;
use DomainException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
//        $exception->getMessage() // @todo log

        $event->setResponse(
            match (true) {
                $exception instanceof HttpExceptionInterface => HttpErrorJsonResponse::createError($exception),
                $exception instanceof ValidationException => ValidationErrorJsonResponse::createError($exception),
                $exception instanceof DomainException => DomainErrorJsonResponse::createError($exception),
                default => UnknownErrorJsonResponse::createError()
            }
        );
    }
}

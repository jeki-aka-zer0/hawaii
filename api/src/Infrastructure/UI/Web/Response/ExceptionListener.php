<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Response;

use App\Infrastructure\UI\Web\Request\ValidationException;
use DomainException;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class ExceptionListener
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        switch ($exception::class) {
            case $exception instanceof HttpExceptionInterface:
                /** @var HttpExceptionInterface $exception */
                $response = HttpErrorJsonResponse::createError($exception);
                $level = Logger::ERROR;
                $verbose = true;
                break;
            case $exception instanceof ValidationException:
                /** @var ValidationException $exception */
                $response = ValidationErrorJsonResponse::createError($exception);
                $level = Logger::INFO;
                $verbose = false;
                break;
            case $exception instanceof DomainException:
                /** @var DomainException $exception */
                $response = DomainErrorJsonResponse::createError($exception);
                $level = Logger::ERROR;
                $verbose = true;
                break;
            default:
                $response = UnknownErrorJsonResponse::createError();
                $level = Logger::CRITICAL;
                $verbose = true;
                break;
        }

        $this->logger->log(
            $level,
            $exception->getMessage(),
            $verbose ? ['code' => $exception->getCode(), 'trace' => $exception->getTraceAsString()] : [],
        );

        $event->setResponse($response);
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Response;

use App\Domain\Shared\Repository\EntityNotFoundException;
use App\Domain\Shared\Repository\FieldException;
use App\Infrastructure\UI\Web\Request\ValidationException;
use DomainException;
use JetBrains\PhpStorm\ArrayShape;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

final class ExceptionListener
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $context = [];

        switch (true) {
            case $exception instanceof HttpExceptionInterface:
                /** @var HttpExceptionInterface $exception */
                $response = HttpErrorJsonResponse::createError($exception);
                $level = Logger::ERROR;
                $context = $this->getExceptionCodeAndTraceData($exception);
                break;
            case $exception instanceof ValidationException:
                /** @var ValidationException $exception */
                $response = ValidationErrorJsonResponse::createError($exception);
                $level = Logger::INFO;
                break;
            case $exception instanceof EntityNotFoundException:
                /** @var EntityNotFoundException $exception */
                $response = HttpErrorJsonResponse::createNotFoundError($exception);
                $level = Logger::ERROR;
                $context = ['entity_id' => $exception->id->getValue()] + $this->getExceptionCodeAndTraceData($exception);
                break;
            case $exception instanceof FieldException:
                $response = DomainErrorJsonResponse::createFieldError($exception);
                $level = Logger::ERROR;
                $context = ['field' => $exception->getField()] + $this->getExceptionCodeAndTraceData($exception);
                break;
            case $exception instanceof DomainException:
                /** @var DomainException $exception */
                $response = DomainErrorJsonResponse::createError($exception);
                $level = Logger::ERROR;
                $context = $this->getExceptionCodeAndTraceData($exception);
                break;
            default:
                $response = UnknownErrorJsonResponse::createError();
                $level = Logger::CRITICAL;
                $context = $this->getExceptionCodeAndTraceData($exception);
                break;
        }

        $this->logger->log($level, $exception->getMessage(), $context);

        $event->setResponse($response);
    }

    #[ArrayShape(['code' => 'int', 'trace' => 'string'])]
    private function getExceptionCodeAndTraceData(Throwable $exception): array
    {
        return ['code' => $exception->getCode(), 'trace' => $exception->getTraceAsString()];
    }
}

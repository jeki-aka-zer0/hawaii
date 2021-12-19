<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Request;

use JetBrains\PhpStorm\Pure;
use LogicException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

final class ValidationException extends LogicException
{
    #[Pure]
    public function __construct(
        private ConstraintViolationListInterface $violations,
        string $message = 'Invalid input.',
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return ConstraintViolationListInterface|ConstraintViolation[]
     */
    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}

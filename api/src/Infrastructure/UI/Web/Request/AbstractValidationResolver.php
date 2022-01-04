<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Request;

use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractValidationResolver implements ArgumentValueResolverInterface
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        $requestDto = $this->getRequestDto($request, $argument);
        $constraints = $this->validator->validate($requestDto);
        if ($constraints->count() > 0) {
            throw new ValidationException($constraints);
        }

        yield $requestDto;
    }

    abstract protected function getRequestDto(Request $request, ArgumentMetadata $argument): RequestDtoInterface;
}

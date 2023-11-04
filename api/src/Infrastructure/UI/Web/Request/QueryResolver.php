<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Request;

use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class QueryResolver extends AbstractValidationResolver
{
    #[Pure]
    public function __construct(ValidatorInterface $validator, private readonly SerializerInterface $serializer)
    {
        parent::__construct($validator);
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $request->isMethod(Request::METHOD_GET) && (new ReflectionClass($argument->getType()))
                ->implementsInterface(QueryInterface::class);
    }

    protected function getRequestDto(Request $request, ArgumentMetadata $argument): QueryInterface
    {
        try {
            /** @noinspection PhpUnhandledExceptionInspection */
            return $this->serializer->denormalize(
                $request->query->all() + $request->attributes->all(),
                $argument->getType(),
                CsvEncoder::FORMAT, // hack to cast string to int. In query all the parameters come as strings
            );
        } catch (NotNormalizableValueException $e) {
            throw ValidationException::unexpectedParameterType($e);
        }
    }
}

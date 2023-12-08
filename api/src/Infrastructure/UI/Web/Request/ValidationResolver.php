<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Request;

use Generator;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class ValidationResolver implements ValueResolverInterface
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        switch ($request->getMethod()) {
            case Request::METHOD_POST && $this->getArgumentReflection($argument)->implementsInterface(CommandInterface::class):
                $dtoClass = $argument->getType();
                yield empty($request->getContent())
                    ? new $dtoClass()
                    : $this->serializer->deserialize($request->getContent(), $dtoClass, JsonEncoder::FORMAT);
                break;
            case Request::METHOD_GET && $this->getArgumentReflection($argument)->implementsInterface(QueryInterface::class):
                try {
                    yield $this->serializer->denormalize(
                        $request->query->all() + $request->attributes->all(),
                        $argument->getType(),
                        CsvEncoder::FORMAT, // hack to cast string to int. In query all the parameters come as strings
                    );
                } catch (NotNormalizableValueException $e) {
                    throw ValidationException::unexpectedParameterType($e);
                }
                break;
        }
    }

    private function getArgumentReflection(ArgumentMetadata $argument): ReflectionClass
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return new ReflectionClass($argument->getType());
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Request;

use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class BodyResolver extends AbstractValidationResolver
{
    #[Pure]
    public function __construct(ValidatorInterface $validator, private readonly SerializerInterface $serializer)
    {
        parent::__construct($validator);
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $request->isMethod(Request::METHOD_POST) && (new ReflectionClass($argument->getType()))
                ->implementsInterface(CommandInterface::class);
    }

    protected function getRequestDto(Request $request, ArgumentMetadata $argument): CommandInterface
    {
        $dtoClass = $argument->getType();

        return empty($request->getContent())
            ? new $dtoClass()
            : $this->serializer->deserialize($request->getContent(), $dtoClass, JsonEncoder::FORMAT);
    }
}

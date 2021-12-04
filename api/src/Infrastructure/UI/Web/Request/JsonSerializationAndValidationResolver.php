<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Request;

use Generator;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class JsonSerializationAndValidationResolver implements ArgumentValueResolverInterface
{
    public function __construct(private SerializerInterface $serializer, private ValidatorInterface $validator)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return (new ReflectionClass($argument->getType()))
            ->implementsInterface(CommandInterface::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        $command = $this->getCommand($request, $argument);
        $constraints = $this->validator->validate($command);
        if ($constraints->count() > 0) {
            throw new ValidationException($constraints);
        }

        yield $command;
    }

    private function getCommand(Request $request, ArgumentMetadata $argument): CommandInterface
    {
        $dtoClass = $argument->getType();

        return empty($request->getContent())
            ? new $dtoClass()
            : $this->serializer->deserialize($request->getContent(), $dtoClass, 'json');
    }
}

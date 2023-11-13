<?php

declare(strict_types=1);

namespace App\Infrastructure\UI\Web\Response\Pagination;

use App\Application\EAV\Entity\Read\Query;
use App\Application\Shared\ListDTO;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class Paginator
{
    public const KEY_OFFSET = 'offset';
    public const KEY_LIMIT = 'limit';
    private array $parameters;

    public function __construct(
        readonly private Query $query,
        readonly private string $routeName,
        private readonly UrlGeneratorInterface $router,
        readonly private ListDTO $list,
    ) {
    }

    public function build(): PaginationDecoratorDTO
    {
        return new PaginationDecoratorDTO(
            $this->list,
            $this->getPrevious(),
            $this->getNext()
        );
    }

    private function getPrevious(): ?string
    {
        return $this->isCurrentPageFirst()
            ? null
            : $this->generate(max(0, $this->query->offset - $this->query->limit));
    }

    private function isCurrentPageFirst(): bool
    {
        return $this->query->offset === 0;
    }

    private function getNext(): ?string
    {
        return $this->isCurrentPageLast()
            ? null
            : $this->generate($this->query->offset + $this->query->limit);
    }

    private function isCurrentPageLast(): bool
    {
        return $this->query->offset + $this->query->limit >= $this->list->count;
    }

    private function generate(int $offset): string
    {
        return $this->router->generate(
            $this->routeName,
            [self::KEY_OFFSET => $offset] + $this->getParameters(),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    private function getParameters(): array
    {
        return $this->parameters ??= [self::KEY_LIMIT => $this->query->limit] + $this->query->toArray();
    }
}

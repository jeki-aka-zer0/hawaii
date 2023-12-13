<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\UI\Web;

use App\Application\Shared\ListDTO;
use App\Infrastructure\UI\Web\Response\Pagination\PaginationDecoratorDTO;
use App\Tests\Integration\AbstractIntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractEndpointTestCase extends AbstractIntegrationTestCase
{
    protected const EXPECTED_RESPONSE_NONE = [
        ListDTO::KEY_COUNT => 0,
        ListDTO::KEY_RESULTS => [],
        PaginationDecoratorDTO::KEY_PREVIOUS => null,
        PaginationDecoratorDTO::KEY_NEXT => null,
    ];

    protected function assertSuccessfulJson(Response $response): array
    {
        self::assertTrue($response->isSuccessful());
        self::assertEquals('application/json', $response->headers->get('content-type'));
        return json_decode($response->getContent(), true, flags: JSON_THROW_ON_ERROR);
    }
}
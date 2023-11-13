<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\UI\Web;

use App\Application\Shared\ListDTO;
use App\Tests\Integration\AbstractIntegrationTest;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractEndpointTest extends AbstractIntegrationTest
{
    protected const EXPECTED_RESPONSE_NONE = [
        ListDTO::KEY_COUNT => 0,
        ListDTO::KEY_RESULTS => [],
    ];

    protected function assertSuccessfulJson(Response $response): array
    {
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        return json_decode($response->getContent(), true, flags: JSON_THROW_ON_ERROR);
    }
}
<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

final class HomeController
{
    public function index(): Response
    {
        return new Response('Yo');
    }
}

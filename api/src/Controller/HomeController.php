<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController
{
    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function index(): Response
    {
        return new Response('Yo');
    }
}

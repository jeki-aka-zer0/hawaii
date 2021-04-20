<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RequiresUserCredits()
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'name' => 'Pet project of Evgeniy Zhukov ',
            'version' => '1.0',
        ]);
    }
}
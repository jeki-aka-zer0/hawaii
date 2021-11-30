<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->prepare('select version();');
        $res = $stmt->execute();
        var_dump($res->fetchAll()); exit;

        return new Response('Yo');
    }
}

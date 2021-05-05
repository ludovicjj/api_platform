<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PingController
 * @package App\Controller
 *
 * @Route("/api/ping", name="get_ping", methods={"GET"})
 */
class PingController
{
    public function __invoke(): JsonResponse
    {
        $ping = rand(5, 15);
        return new JsonResponse(['ping' => $ping]);
    }
}
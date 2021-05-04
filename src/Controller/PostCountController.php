<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class PostCountController
{
    public function __invoke($data)
    {
        $nbPost = count($data);
        $data = ['nbPosts' => $nbPost];

        return new JsonResponse($data);
    }
}
<?php


namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;

class PostCountController
{
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function __invoke(Request $request): int
    {
        $online = $request->get('online');
        $condition = [];
        if ($online !== null) {
            $condition = ['online' => $online === '1'];
        }

        return $this->postRepository->count($condition);
    }
}
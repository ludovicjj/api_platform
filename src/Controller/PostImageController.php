<?php


namespace App\Controller;


use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;

class PostImageController
{
    public function __invoke(Request $request)
    {
        $post = $request->attributes->get('data');
        $file = $request->files->get('file');
        dd($file, $request);
    }
}
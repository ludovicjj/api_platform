<?php


namespace App\Controller;


use App\Entity\Post;
use http\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Request;

class PostImageController
{
    public function __invoke(Request $request)
    {
        $post = $request->attributes->get('data');

        if (!$post instanceof Post) {
            throw new RuntimeException('expected object instance of App\Entity\Post');
        }

        // Custom DeserializeListener handle denormalize is content-type is multipart.
        // And include $_POST and $_FILES when denormalize.
        // Property file is linked to groups: write:post

        // $post->setFile($request->files->get('file'));

        // It is required that at least one field changes if you are using doctrine
        // otherwise the event listeners won't be called and the file is lost
        $post->setUpdatedAt(new \DateTime());

        return $post;
    }
}
<?php


namespace App\Serializer;


use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

class FileDenormalizer implements ContextAwareDenormalizerInterface
{
    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        return $data instanceof File;
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        // return UploadedFile without denormalize
        return $data;
    }
}
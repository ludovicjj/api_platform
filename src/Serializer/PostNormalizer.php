<?php


namespace App\Serializer;


use App\Entity\Post;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Vich\UploaderBundle\Storage\StorageInterface;

class PostNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;
    private const ALREADY_CALLED = 'PostNormalizerAlreadyCalled';
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        $isAlreadyCalled = $context[self::ALREADY_CALLED] ?? false;

        return $data instanceof Post && $isAlreadyCalled === false;
    }


    public function normalize($object, string $format = null, array $context = [])
    {
        $object->setFileUrl($this->storage->resolveUri($object, 'file'));
        $context[self::ALREADY_CALLED] = true;
        return $this->normalizer->normalize($object, $format, $context);
    }
}
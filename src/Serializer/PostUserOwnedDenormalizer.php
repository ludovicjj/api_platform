<?php

namespace App\Serializer;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\UserOwnedInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class PostUserOwnedDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED_DENORMALIZER = 'UserOwnedDenormalizerCalled';
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        $isAlreadyCalled = $context[self::ALREADY_CALLED_DENORMALIZER] ?? false;

        return ($type === Post::class && $isAlreadyCalled === false);
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        // Circular reference detected :
        // Denormalizer when denormalize -> call each denormalizer to check who support.
        // UserOwnedDenormalizer support !
        // UserOwnedDenormalizer call Denormalizer to denormalize
        // Denormalizer when denormalize -> call each denormalizer to check who support.
        // ... infinite loop

        // Solution :
        // Define new key into context with value: true
        // Denormalizer when denormalize -> call each denormalizer to check who support.
        // UserOwnedDenormalizer support Post::class but const is not false
        // end loop

        $context[self::ALREADY_CALLED_DENORMALIZER] = true;

        /** @var UserOwnedInterface $object */
        $object = $this->denormalizer->denormalize($data, $type, $format, $context);
        /** @var User|null $user */
        $user = $this->security->getUser();
        $object->setUser($user);

        return $object;
    }
}
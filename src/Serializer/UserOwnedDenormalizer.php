<?php

namespace App\Serializer;

use App\Entity\User;
use App\Entity\UserOwnedInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class UserOwnedDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
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
        $isAlreadyCalled = $data[self::ALREADY_CALLED_DENORMALIZER] ?? false;

        // Check if type is subclass of App\Entity\UserOwnedInterface
        $isSubClass = is_subclass_of($type, UserOwnedInterface::class, true);

        return ($isSubClass && $isAlreadyCalled === false);
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        // Circular reference detected :
        // Denormalizer when denormalize -> call each denormalizer to check who support.
        // UserOwnedDenormalizer support !
        // UserOwnedDenormalizer call Denormalizer to denormalize
        // Denormalizer when denormalize -> call each denormalizer to check who support.
        // ... infinite loop

        // Solution with data[]:
        // Define new key into data[key] = true
        // Denormalizer when denormalize -> call each denormalizer to check who support.
        // UserOwnedDenormalizer support Post::class BUT $data[key] !== false
        // end loop

        // Solution with context[]:
        // Define new key into context[key] = true, this key MUST UNIQUE
        // In each loop context is shared.

        $data[self::ALREADY_CALLED_DENORMALIZER] = true;
        // $context[$this->createCustomKey($type)] = true;

        /** @var UserOwnedInterface $object */
        $object = $this->denormalizer->denormalize($data, $type, $format, $context);
        /** @var User|null $user */
        $user = $this->security->getUser();
        $object->setUser($user);

        return $object;
    }
}
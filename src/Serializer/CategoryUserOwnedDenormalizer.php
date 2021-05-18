<?php


namespace App\Serializer;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\UserOwnedInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class CategoryUserOwnedDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    private const ALREADY_CALLED_DENORMALIZER = 'CategoryWithUserOwnedDenormalizer';
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        $isAlreadyCalled = $context[self::ALREADY_CALLED_DENORMALIZER] ?? false;
        return ($type === Category::class && $isAlreadyCalled === false);
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED_DENORMALIZER] = true;
        /** @var UserOwnedInterface $obj */
        $obj = $this->denormalizer->denormalize($data, $type, $format, $context);
        /** @var User|null $user */
        $user = $this->security->getUser();
        $obj->setUser($user);

        return $obj;
    }
}
<?php


namespace App\Serializer;


use App\Entity\Post;
use App\Security\Voter\PostVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class PostApiNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;
    private const ALREADY_CALLED_NORMALIZER = 'PostApiNormalizerAlreadyCalled';
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        $isAlreadyCalled = $context[self::ALREADY_CALLED_NORMALIZER] ?? false;

        return $data instanceof Post && $isAlreadyCalled === false;
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED_NORMALIZER] = true;
        if (
            $this->authorizationChecker->isGranted(PostVoter::CAN_VIEW, $object) &&
            isset($context['groups'])
        ) {
            $context['groups'][] = 'read:posts:User';
        }

        return $this->normalizer->normalize($object, $format, $context);
    }
}
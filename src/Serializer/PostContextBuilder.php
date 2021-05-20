<?php


namespace App\Serializer;


use ApiPlatform\Core\Exception\RuntimeException;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\Post;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\Request;

class PostContextBuilder implements SerializerContextBuilderInterface
{
    /** @var SerializerContextBuilderInterface $decorated */
    private $decorated;

    /** @var AuthorizationCheckerInterface $authorizationChecker */
    private $authorizationChecker;

    public function __construct(
        SerializerContextBuilderInterface $decorated,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        // If resourceClass is Post and current user has ROLE_USER
        // Add into context groups read:posts:User
        // Context add to normalize field slug and online

        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;
        if (
            $resourceClass === Post::class &&
            isset($context['groups']) &&
            $this->authorizationChecker->isGranted('ROLE_USER')
        ) {
            $context['groups'][] = 'read:posts:User';
        }

        return $context;
    }
}
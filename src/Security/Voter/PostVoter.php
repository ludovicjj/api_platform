<?php


namespace App\Security\Voter;


use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoter extends Voter
{
    public const CAN_VIEW = 'CAN_VIEW';

    protected function supports(string $attribute, $subject)
    {
        if (!in_array($attribute, [self::CAN_VIEW])) {
            return false;
        }

        if (!$subject instanceof Post) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        // if user is anonymous, do not grand access
        if (!$user instanceof User) {
            return false;
        }

        /** @var Post $post */
        $post = $subject;

        switch ($attribute) {
            case self::CAN_VIEW :
                return $this->canEdit($post, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Post $post, User $user): bool
    {
        $owner = $post->getUser();
        if ($owner && $owner->getId() === $user->getId()) {
            return true;
        }
        return false;
    }
}
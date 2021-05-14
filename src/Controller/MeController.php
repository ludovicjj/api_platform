<?php


namespace App\Controller;

use Symfony\Component\Security\Core\Security;

class MeController
{
    /** @var Security $security */
    private $security;

    public function __construct(
        Security $security
    )
    {
        $this->security = $security;
    }

    public function __invoke()
    {
        // let api_plarform serialize it by normalization_context
        // User returned by security is the user created into User::createFromPayload
        return $this->security->getUser();
    }
}
<?php


namespace App\Controller;


use Symfony\Component\Security\Core\Security;

class MeController
{
    /** @var Security $security */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke()
    {
        $user = $this->security->getUser();
        // let api_plarform serialize it by normalization_context
        return $user;
    }
}
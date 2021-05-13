<?php

namespace App\Subscriber;

use Gesdinet\JWTRefreshTokenBundle\Event\RefreshEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RefreshSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            'gesdinet.refresh_token' => 'onRefreshToken'
        ];
    }

    public function onRefreshToken(RefreshEvent $event)
    {

    }
}
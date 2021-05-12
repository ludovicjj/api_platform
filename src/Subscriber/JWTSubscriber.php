<?php


namespace App\Subscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onJwtCreated'
        ];
    }

    public function onJwtCreated(JWTCreatedEvent $event)
    {
        $data = $event->getData();
        $data['email'] = $event->getUser()->getUsername();
        $event->setData($data);
    }
}
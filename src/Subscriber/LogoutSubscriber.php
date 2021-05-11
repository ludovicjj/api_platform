<?php

namespace App\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    public const AVAILABLE_CONTENT_TYPES = [
        'application/json',
        '*/*'
    ];

    public static function getSubscribedEvents()
    {
        return [
            LogoutEvent::class => 'onLogoutEvent'
        ];
    }

    public function onLogoutEvent(LogoutEvent $event)
    {
        foreach ($event->getRequest()->getAcceptableContentTypes() as $contentType) {
            if (in_array($contentType, self::AVAILABLE_CONTENT_TYPES)) {
                $event->setResponse(new JsonResponse(null, Response::HTTP_NO_CONTENT));
                break;
            }
        }
    }
}
<?php


namespace App\Listener;

use ApiPlatform\Core\EventListener\DeserializeListener as DecoratedListener;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DeserializeListener
{
    private $decorated;
    private $contextBuilder;
    private $denormalizer;

    public function __construct(
        DecoratedListener $decorated,
        SerializerContextBuilderInterface $contextBuilder,
        DenormalizerInterface $denormalizer
    ) {
        $this->decorated = $decorated;
        $this->contextBuilder = $contextBuilder;
        $this->denormalizer = $denormalizer;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if ($request->isMethodCacheable() || $request->isMethod(Request::METHOD_DELETE)) {
            return;
        }

        if ($request->getContentType() === 'multipart') {
            $this->denormalizeFromRequest($request);
        } else {
            $this->decorated->onKernelRequest($event);
        }
    }

    private function denormalizeFromRequest(Request $request): void
    {
        $attributes = RequestAttributesExtractor::extractAttributes($request);

        if (empty($attributes)) {
            return;
        }

        $context = $this->contextBuilder->createFromRequest($request, false, $attributes);
        $post = $request->request->all();
        $file = $request->files->all();
        $populated = $request->attributes->get('data');
        if ($populated !== null) {
            $context['object_to_populate'] = $populated;
        }

        $object = $this->denormalizer->denormalize(
            array_merge($post, $file),
            $attributes['resource_class'],
            null,
            $context
        );

        $request->attributes->set('data', $object) ;
    }
}
<?php


namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\Response;
use ApiPlatform\Core\OpenApi\OpenApi;

class OpenApiFactory implements OpenApiFactoryInterface
{
    private $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
        $pathItem = $openApi->getPaths()->getPath('/api/categories/{id}');
        $openApi->getPaths()->addPath('/api/categories/{id}', $pathItem->withGet(null));

        // Create new entry GET : /api/ping
        $response = new Response(
            'Renvoi un ping',
            new \ArrayObject([
                "application/json" => [
                    "schema" => [
                        "type" => "object",
                        "properties" => [
                            "ping" => [
                                "type" => "integer"
                            ]
                        ]
                    ]
                ]
            ]),
            null,
            null
        );

        $operation = new Operation(
            'getPing',
            ['Ping'],
            [200 => $response],
            'Obtenir un ping aléatoire',
            "Une opération personnalisé qui renvoi un ping aléatoire"
        );

        $path = new PathItem(null, null, null, $operation);
        $openApi->getPaths()->addPath('/api/ping', $path);
        return $openApi;
    }
}
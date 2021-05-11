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

        // Remove path when summary is hidden
        /**
         * @var string $uri
         * @var PathItem $path
         */
        foreach($openApi->getPaths()->getPaths() as $uri => $path) {
            if ($path->getGet() && $path->getGet()->getSummary() === 'hidden') {
                $openApi->getPaths()->addPath($uri, $path->withGet(null));
            }
        }

        // auth cookie
        $schemes = $openApi->getComponents()->getSecuritySchemes();
        $schemes['cookieAuth'] = new \ArrayObject([
            'type' => 'apiKey',
            'in' => 'cookie',
            'name' => 'PHPSESSID'
        ]);

        //Update entry GET: /api/me
        $meOperation = $openApi->getPaths()->getPath('/api/me')->getGet()->withParameters([]);
        $meOperation->addResponse(new Response("Unauthorized"), 401);
        $mePath = $openApi->getPaths()->getPath('/api/me')->withGet($meOperation);
        $openApi->getPaths()->addPath('/api/me', $mePath);


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
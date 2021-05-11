<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\Model\Response;
use ApiPlatform\Core\OpenApi\OpenApi;

class AuthApiFactory implements OpenApiFactoryInterface
{
    private $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
        $schemas = $openApi->getComponents()->getSchemas();
        $schemas['Credentials'] = new \ArrayObject([
           'type' => 'object',
           'properties' => [
               'username' => [
                   'type' => 'string',
                   'example' => 'john@doe.fr'
               ],
               'password' => [
                   'type' => 'string',
                   'example' => 'secret'
               ]
           ]
        ]);

        // Create new entry POST : /api/login
        $operation = new Operation(
            'postLoginApi',
            ['Authentication'],
            [],
            "Login",
            "Provide credentials to login",
            null,
            [],
            new RequestBody(
                'Your credentials',
                new \ArrayObject([
                    'application/json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/Credentials'
                        ]
                    ]
                ])
            )
        );
        $operation->addResponse(
            new Response(
                'authentication successful',
                new \ArrayObject([
                    'application/json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/User-read.user'
                        ]
                    ]
                ])
            ),
            200
        );
        $operation->addResponse(
            new Response(
                'authentication fail',
                new \ArrayObject([
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'error' => [
                                    'type' => 'string'
                                ]
                            ]
                        ]
                    ]
                ])
            ),
            401
        );

        $pathItem = new PathItem(null, null, null, null, null, $operation);
        $openApi->getPaths()->addPath('/api/login', $pathItem);

        return $openApi;
    }
}
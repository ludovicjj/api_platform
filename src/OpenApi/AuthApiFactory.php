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

        // Create new path POST : /api/login
        $loginOperation = new Operation();
        $loginOperation = $loginOperation
            ->withOperationId('postLoginApi')
            ->withTags(['Authentication'])
            ->withSummary("Login")
            ->withDescription("Provide credentials to login")
            ->withRequestBody(
                new RequestBody(
                    'Your credentials',
                    new \ArrayObject([
                        'application/json' => [
                            'schema' => [
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
                            ]
                        ]
                    ])
                )
            )
            ->addResponse(
                new Response(
                    'Authentication successful',
                    new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'email' => [
                                        'type' => 'string',
                                        'example' => 'john@doe.fr'
                                    ],
                                    'roles' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'string'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ])
                ),
                200
            )
            ->addResponse(
                new Response(
                    'Authentication fail',
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
        $loginPath = new PathItem();
        $loginPath = $loginPath->withPost($loginOperation);


        // Create new path POST : /api/logout
        $logoutOperation = new Operation();
        $logoutOperation = $logoutOperation
            ->withOperationId('postLogoutApi')
            ->withTags(['Authentication'])
            ->withSummary('Logout')
            ->withDescription('Logout current User')
            ->addResponse(new Response('Logout success'), 204);
        $logoutPath = new PathItem();
        $logoutPath = $logoutPath->withPost($logoutOperation);


        $openApi->getPaths()->addPath('/logout', $logoutPath);
        $openApi->getPaths()->addPath('/api/login', $loginPath);

        return $openApi;
    }
}
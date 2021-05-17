<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\Model\Response;
use ApiPlatform\Core\OpenApi\OpenApi;
use ArrayObject;

class JwtDecorator implements OpenApiFactoryInterface
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
        $schemas['Token'] = new ArrayObject([
           'type' => 'object',
           'properties' => [
               'token' => [
                   'type' => 'string',
                   'readonly' => true
               ]
           ]
        ]);
        $schemas['Post-input'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'title' => [
                    'type' => 'string',
                    'minLength' => 5,
                    'description' => "Nom de l'article",
                    'example' => 'My post title'
                ],
                'slug' => [
                    'type' => 'string',
                    'description' => "Slug de l'article",
                    'example' => 'my-post-title'
                ],
                'content' => [
                    'type' => 'string',
                    'description' => "Le contenu de l'article",
                    'example' => "my content"
                ],
                'category' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => [
                            'type' => 'string',
                            'minLength' => 3,
                            'description' => 'Nom de la catégorie',
                            'example' => 'my awesome categorie'
                        ]
                    ],
                    'required' => [
                        'name'
                    ]
                ]
            ],
            'required' => [
                'title',
                'slug',
                'content'
            ]
        ]);

        $schemas['Credentials'] = new ArrayObject([
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

        $schemas['Error'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'integer'
                ],
                'message' => [
                    'type' => 'string'
                ]
            ]
        ]);

        // Create new path POST : /api/login
        $loginOperation = (new Operation())
            ->withOperationId('postLoginApi')
            ->withTags(['Authentication'])
            ->withSummary("Get JWT token to login.")
            ->withRequestBody(
                new RequestBody(
                    'Generate new JWT Token',
                    new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials'
                            ]
                        ]
                    ])
                )
            )
            ->addResponse(
                new Response(
                    'Get JWT token',
                    new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Token'
                            ]
                        ]
                    ])
                ),
                200
            )
            ->addResponse(
                new Response(
                    'Authentication fail',
                    new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Error'
                            ]
                        ]
                    ])
                ),
                401
            );
        $loginPath = (new PathItem())->withPost($loginOperation);


        // Create new path POST : /api/logout
        // $logoutOperation = (new Operation())
        //     ->withOperationId('postLogoutApi')
        //     ->withTags(['Authentication'])
        //     ->withSummary('Logout')
        //     ->withDescription('Logout current User')
        //     ->addResponse(new Response('Logout success'), 204);
        // $logoutPath = (new PathItem())->withPost($logoutOperation);
        // $openApi->getPaths()->addPath('/logout', $logoutPath);


        $openApi->getPaths()->addPath('/api/login', $loginPath);

        return $openApi;
    }
}
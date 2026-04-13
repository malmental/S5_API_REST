<?php

// Only the most common configs are shown. See the https://scribe.knuckles.wtf/laravel/reference/config for all.

return [
    // The HTML <title> for the generated documentation.
    'title' => 'INCIDENsly 𝒘ebApp API Documentation',

    // A short description of your API. Will be included in the docs webpage, Postman collection and OpenAPI spec.
    'description' => <<<'DESC'
        
    Documentation for the INCIDENsly 𝒘ebApp API.

    This API provides endpoints for managing incidents, comments, tags, and user administration.
    Authentication is handled via OAuth2 (Bearer token). Use the /login endpoint to obtain your token.
    All responses are returned in JSON format.

    ## Quick Start

    1. Register a new user via the /register endpoint to create an account and obtain an API token.
    2. Use the /login endpoint to authenticate and receive an access token.
    3. Include the token in the Authorization header (e.g. `Authorization: Bearer {YOUR_TOKEN}`) for all subsequent requests to protected endpoints.
    4. Explore the available endpoints for managing incidences, comments, tags, and users.
    5. Refer to the endpoint documentation for details on request parameters, response formats, and example requests and responses.

    ## Roles

    - `user` — Can manage their own incidents, comments, and tags.
    - `admin` — has additional access to user management.
    DESC,

    // Text to place in the "Introduction" section, right after the `description`. Markdown and HTML are supported.
    'intro_text' => '',

    // The base URL displayed in the docs.
    'base_url' => config('app.url'),

    // Routes to include in the docs
    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/v1/*'],
                'domains' => ['*'],
            ],
            'include' => [],
            'exclude' => [
                'api/documentation*',
            ],
        ],
    ],

    // Using 'static' type - docs served from public/docs/
    'type' => 'static',

    'theme' => 'elements',

    'static' => [
        'output_path' => 'public/docs',
    ],

    'try_it_out' => [
        'enabled' => true,
        'base_url' => env('APP_URL', 'http://127.0.0.1:8000'),
        'use_csrf' => false,
        'csrf_url' => '/sanctum/csrf-cookie',
    ],

    'auth' => [
        'enabled' => true,
        'default' => false,
        'in' => 'bearer',
        'name' => 'Authorization',
        'use_value' => env('SCRIBE_AUTH_KEY'),
        'placeholder' => '{YOUR_AUTH_KEY}',
        'extra_info' => 'You can retrieve your token by visiting your dashboard and clicking <b>Generate API token</b>.',
    ],

    'example_languages' => [
        'bash',
        'javascript',
    ],

    'postman' => [
        'enabled' => true,
        'overrides' => [],
    ],

    'openapi' => [
        'enabled' => true,
        'version' => '3.0.3',
        'overrides' => [],
        'generators' => [],
    ],

    'groups' => [
        'default' => 'Endpoints',
        'order' => [],
    ],

    'logo' => false,

    'last_updated' => 'Last updated: {date:F j, Y}',

    'examples' => [
        'faker_seed' => 1234,
        'models_source' => ['factoryCreate', 'factoryMake', 'databaseFirst'],
    ],

    'strategies' => [
        'metadata' => ['Knuckles\Scribe\Extracting\Strategies\Metadata\GetFromDocBlocks'],
        'headers' => ['Knuckles\Scribe\Extracting\Strategies\Headers\GetFromHeaderTag'],
        'urlParameters' => ['Knuckles\Scribe\Extracting\Strategies\UrlParameters\GetFromUrlParamTag'],
        'queryParameters' => ['Knuckles\Scribe\Extracting\Strategies\QueryParameters\GetFromQueryParamTag'],
        'bodyParameters' => ['Knuckles\Scribe\Extracting\Strategies\BodyParameters\GetFromBodyParamTag'],
        'responses' => ['Knuckles\Scribe\Extracting\Strategies\Responses\UseResponseTag'],
        'responseFields' => ['Knuckles\Scribe\Extracting\Strategies\ResponseFields\GetFromResponseFieldTag'],
    ],

    'database_connections_to_transact' => [config('database.default')],

    'fractal' => [
        'serializer' => null,
    ],
];

<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Assessment Question Two API',
    version: '1.0.0',
    description: 'API documentation for authentication, product, and order management.'
)]
#[OA\Server(
    url: \L5_SWAGGER_CONST_HOST.'/api',
    description: 'Local development server'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token',
    description: 'Use Bearer token from login'
)]
class OpenApi
{
}

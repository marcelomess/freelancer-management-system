<?php

namespace App\Http\Controllers\Api;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Freelancer Management System API',
    description: 'API RESTful para sistema de gestão de freelancers com portfólios e sistema de tickets'
)]
#[OA\Server(
    url: 'http://localhost/api',
    description: 'Servidor de Desenvolvimento'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'token'
)]
#[OA\Tag(name: 'Authentication', description: 'Endpoints de autenticação')]
#[OA\Tag(name: 'Services', description: 'Gerenciamento de serviços/portfólio')]
#[OA\Tag(name: 'Tickets', description: 'Sistema de chamados')]
#[OA\Tag(name: 'Reviews', description: 'Sistema de avaliações')]
#[OA\Tag(name: 'Freelancers', description: 'Perfis de freelancers')]
class OpenApiController
{
    //
}

<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Freelancer Management System API",
 *     version="1.0.0",
 *     description="API RESTful para sistema de gestão de freelancers com portfólios e sistema de tickets",
 *     @OA\Contact(
 *         email="contato@freelancerms.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost/api",
 *     description="Servidor de Desenvolvimento"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="token",
 *     description="Laravel Sanctum Bearer Token"
 * )
 */
abstract class Controller
{
    //
}

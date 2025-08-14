<?php

namespace App\Swagger;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Desafio SESAB API",
 *     description="Documentação da API do projeto Laravel 12",
 *     @OA\Contact(
 *         email="jimmystarlingdev@gmail.com",
 *         name="Jimmy Starling"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Servidor Local"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your bearer token in the format **Bearer &lt;token>**"
 * )
 *
 */
class SwaggerAnnotations
{
    // Este arquivo serve apenas para conter as annotations globais do Swagger.
}


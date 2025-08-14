<?php

/**
 * @OA\Info(
 *     title="API Laravel 12 - User Management",
 *     version="1.0.0",
 *     description="Documentação da API com Swagger / L5-Swagger",
 *     @OA\Contact(
 *         email="contato@minhaapi.com",
 *         name="Jimmy Starling"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Servidor principal da API"
 * )
 */

/**
 * ========================
 * Schemas
 * ========================
 */

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     required={"id","name","email"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string"),
 *     @OA\Property(property="cpf", type="string"),
 *     @OA\Property(property="profile_id", ref="#/components/schemas/Profile"),
 *     @OA\Property(property="address", type="array", @OA\Items(ref="#/components/schemas/Address"))
 * )
 */

/**
 * @OA\Schema(
 *     schema="Profile",
 *     type="object",
 *     title="Profile",
 *     required={"id","name"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="users", type="array", @OA\Items(ref="#/components/schemas/User"))
 * )
 */

/**
 * @OA\Schema(
 *     schema="Address",
 *     type="object",
 *     title="Address",
 *     required={"id","street","city","state","zip"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="street", type="string"),
 *     @OA\Property(property="city", type="string"),
 *     @OA\Property(property="state", type="string"),
 *     @OA\Property(property="zip", type="string"),
 *     @OA\Property(property="users", type="array", @OA\Items(ref="#/components/schemas/User"))
 * )
 */

/**
 * ========================
 * Auth Endpoints
 * ========================
 */

/**
 * @OA\Post(
 *     path="/api/register",
 *     tags={"Auth"},
 *     summary="Register a new user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"name","email","cpf","password","profile_id"},
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string"),
 *             @OA\Property(property="cpf", type="string"),
 *             @OA\Property(property="password", type="string"),
 *             @OA\Property(property="password_confirmation", type="string"),
 *             @OA\Property(property="profile_id", type="integer"),
 *             @OA\Property(property="address", type="array", @OA\Items(ref="#/components/schemas/Address"))
 *         )
 *     ),
 *     @OA\Response(response=201, description="User successfully registered", @OA\JsonContent(ref="#/components/schemas/User")),
 *     @OA\Response(response=400, description="Validation error")
 * )
 */

/**
 * @OA\Post(
 *     path="/api/login",
 *     tags={"Auth"},
 *     summary="Login user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string"),
 *             @OA\Property(property="password", type="string")
 *         )
 *     ),
 *     @OA\Response(response=200, description="User successfully logged in", @OA\JsonContent(ref="#/components/schemas/User")),
 *     @OA\Response(response=401, description="Invalid credentials")
 * )
 */

/**
 * ========================
 * UserController Endpoints
 * ========================
 */

/**
 * @OA\Get(
 *     path="/api/users",
 *     tags={"Users"},
 *     summary="List users",
 *     @OA\Parameter(name="name", in="query", description="Filter by name", required=false, @OA\Schema(type="string")),
 *     @OA\Parameter(name="cpf", in="query", description="Filter by CPF", required=false, @OA\Schema(type="string")),
 *     @OA\Response(response=200, description="List of users", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User")))
 * )
 */

/**
 * @OA\Get(
 *     path="/api/users/{id}",
 *     tags={"Users"},
 *     summary="Get user by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="User retrieved", @OA\JsonContent(ref="#/components/schemas/User")),
 *     @OA\Response(response=404, description="User not found")
 * )
 */

/**
 * @OA\Post(
 *     path="/api/users",
 *     tags={"Users"},
 *     summary="Create new user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *     @OA\Response(response=201, description="User created", @OA\JsonContent(ref="#/components/schemas/User"))
 * )
 */

/**
 * @OA\Put(
 *     path="/api/users/{id}",
 *     tags={"Users"},
 *     summary="Update user by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/User")),
 *     @OA\Response(response=200, description="User updated", @OA\JsonContent(ref="#/components/schemas/User")),
 *     @OA\Response(response=403, description="Unauthorized")
 * )
 */

/**
 * @OA\Delete(
 *     path="/api/users/{id}",
 *     tags={"Users"},
 *     summary="Delete user by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=204, description="User deleted")
 * )
 */

/**
 * ========================
 * ProfileController Endpoints
 * ========================
 */

/**
 * @OA\Get(
 *     path="/api/profiles",
 *     tags={"Profiles"},
 *     summary="List profiles",
 *     @OA\Response(response=200, description="List of profiles", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Profile")))
 * )
 */

/**
 * @OA\Get(
 *     path="/api/profiles/{id}",
 *     tags={"Profiles"},
 *     summary="Get profile by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Profile retrieved", @OA\JsonContent(ref="#/components/schemas/Profile")),
 *     @OA\Response(response=404, description="Profile not found")
 * )
 */

/**
 * @OA\Post(
 *     path="/api/profiles",
 *     tags={"Profiles"},
 *     summary="Create profile",
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/Profile")),
 *     @OA\Response(response=201, description="Profile created", @OA\JsonContent(ref="#/components/schemas/Profile"))
 * )
 */

/**
 * @OA\Put(
 *     path="/api/profiles/{id}",
 *     tags={"Profiles"},
 *     summary="Update profile by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/Profile")),
 *     @OA\Response(response=200, description="Profile updated", @OA\JsonContent(ref="#/components/schemas/Profile"))
 * )
 */

/**
 * @OA\Delete(
 *     path="/api/profiles/{id}",
 *     tags={"Profiles"},
 *     summary="Delete profile by ID",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=204, description="Profile deleted")
 * )
 */

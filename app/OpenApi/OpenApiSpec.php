<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        version: '1.0.0',
        title: 'Veterinaria API',
        description: 'Documentación OpenAPI de la API de veterinaria.',
    ),
    servers: [
        new OA\Server(
            url: '/',
            description: 'Servidor principal',
        ),
    ],
    tags: [
        new OA\Tag(name: 'Autenticación', description: 'Endpoints de autenticación JWT'),
        new OA\Tag(name: 'Usuarios', description: 'Gestión de usuarios y roles'),
        new OA\Tag(name: 'Mascotas', description: 'Gestión de mascotas'),
        new OA\Tag(name: 'Imágenes', description: 'Gestión de imágenes de mascotas'),
        new OA\Tag(name: 'Vacunas', description: 'Gestión de vacunas'),
        new OA\Tag(name: 'Estadísticas', description: 'Dashboard y reportes'),
    ],
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'JWT Bearer token. Usa el formato: Bearer {token}',
)]
#[OA\Schema(
    schema: 'AuthLoginRequest',
    required: ['email', 'password'],
    properties: [
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@veterinaria.com'),
        new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret123'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'UserSummary',
    properties: [
        new OA\Property(property: 'id', type: 'string', example: '01JVMR0M2T6FC7NG8R5GRM5AJV'),
        new OA\Property(property: 'name', type: 'string', example: 'Brayan Cárdenas'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@veterinaria.com'),
        new OA\Property(property: 'role', type: 'string', example: 'ADMIN'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'AuthSuccessResponse',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Inicio de sesión exitoso'),
        new OA\Property(property: 'token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...'),
        new OA\Property(property: 'token_type', type: 'string', example: 'bearer'),
        new OA\Property(property: 'expires_in', type: 'integer', example: 3600),
        new OA\Property(property: 'user', ref: '#/components/schemas/UserSummary'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PetSummary',
    properties: [
        new OA\Property(property: 'id', type: 'string', example: '01JVMR0M2T6FC7NG8R5GRM5AJV'),
        new OA\Property(property: 'name', type: 'string', example: 'Firulais'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'RoleList',
    type: 'array',
    items: new OA\Items(type: 'string'),
    example: ['ADMIN'],
)]
#[OA\Schema(
    schema: 'User',
    properties: [
        new OA\Property(property: 'id', type: 'string', example: '01JVMR0M2T6FC7NG8R5GRM5AJV'),
        new OA\Property(property: 'dni', type: 'string', example: '12345678'),
        new OA\Property(property: 'name', type: 'string', example: 'Brayan Cárdenas'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@veterinaria.com'),
        new OA\Property(property: 'phone', type: 'string', nullable: true, example: '999888777'),
        new OA\Property(property: 'address', type: 'string', nullable: true, example: 'Av. Central 123'),
        new OA\Property(property: 'latitude', type: 'number', format: 'float', nullable: true, example: -12.0464),
        new OA\Property(property: 'longitude', type: 'number', format: 'float', nullable: true, example: -77.0428),
        new OA\Property(property: 'active', type: 'boolean', example: true),
        new OA\Property(property: 'roles', ref: '#/components/schemas/RoleList'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'OwnerSummary',
    properties: [
        new OA\Property(property: 'id', type: 'string', example: '01JVMR0M2T6FC7NG8R5GRM5AJV'),
        new OA\Property(property: 'name', type: 'string', example: 'Juan Pérez'),
        new OA\Property(property: 'phone', type: 'string', nullable: true, example: '999888777'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'Pet',
    properties: [
        new OA\Property(property: 'id', type: 'string', example: '01JVMR0M2T6FC7NG8R5GRM5AJV'),
        new OA\Property(property: 'name', type: 'string', example: 'Firulais'),
        new OA\Property(property: 'species', type: 'string', example: 'Canino'),
        new OA\Property(property: 'race', type: 'string', example: 'Mestizo'),
        new OA\Property(property: 'gender', type: 'string', example: 'MACHO'),
        new OA\Property(property: 'temperament', type: 'string', example: 'Tranquilo'),
        new OA\Property(property: 'reproductive_condition', type: 'string', example: 'ENTERO'),
        new OA\Property(property: 'age', type: 'string', example: '3 años y 4 meses'),
        new OA\Property(property: 'status', type: 'string', example: 'ADOPTADO'),
        new OA\Property(property: 'user', ref: '#/components/schemas/OwnerSummary'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'Vaccine',
    properties: [
        new OA\Property(property: 'id', type: 'string', example: '01JVMR0M2T6FC7NG8R5GRM5AJV'),
        new OA\Property(property: 'type', type: 'string', example: 'Rabia'),
        new OA\Property(property: 'aplication_date', type: 'string', format: 'date', example: '2026-05-18'),
        new OA\Property(property: 'months_validity', type: 'integer', example: 12),
        new OA\Property(property: 'expiration_date', type: 'string', format: 'date', example: '2027-05-18'),
        new OA\Property(property: 'pet', ref: '#/components/schemas/PetSummary'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'PetImage',
    properties: [
        new OA\Property(property: 'id', type: 'string', example: '01JVMR0M2T6FC7NG8R5GRM5AJV'),
        new OA\Property(property: 'pet_id', type: 'string', example: '01JVMR0M2T6FC7NG8R5GRM5AJV'),
        new OA\Property(property: 'path', type: 'string', example: 'pets/abc123.webp'),
        new OA\Property(property: 'url', type: 'string', nullable: true, example: '/storage/pets/abc123.webp'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Vacuna aplicada'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'DashboardStats',
    properties: [
        new OA\Property(property: 'total_pets', type: 'integer', example: 120),
        new OA\Property(property: 'total_owners', type: 'integer', example: 45),
        new OA\Property(property: 'vaccinated_this_month', type: 'integer', example: 17),
        new OA\Property(property: 'overdue_vaccines', type: 'integer', example: 8),
        new OA\Property(property: 'upcoming_in_30_days', type: 'integer', example: 12),
        new OA\Property(property: 'unvaccinated_count', type: 'integer', example: 6),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'MonthlyActivity',
    properties: [
        new OA\Property(property: 'month', type: 'string', example: 'May'),
        new OA\Property(property: 'month_key', type: 'string', example: '2026-05'),
        new OA\Property(property: 'new_pets', type: 'integer', example: 9),
        new OA\Property(property: 'vaccines_applied', type: 'integer', example: 14),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'SpeciesDistribution',
    properties: [
        new OA\Property(property: 'species', type: 'string', example: 'Canino'),
        new OA\Property(property: 'total', type: 'integer', example: 75),
        new OA\Property(property: 'percentage', type: 'number', format: 'float', example: 62.5),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'UnvaccinatedPet',
    properties: [
        new OA\Property(property: 'id', type: 'string', example: '01JVMR0M2T6FC7NG8R5GRM5AJV'),
        new OA\Property(property: 'name', type: 'string', example: 'Firulais'),
        new OA\Property(property: 'species', type: 'string', example: 'Canino'),
        new OA\Property(property: 'race', type: 'string', example: 'Mestizo'),
        new OA\Property(property: 'status', type: 'string', example: 'ADOPTADO'),
        new OA\Property(property: 'registered_at', type: 'string', format: 'date', example: '2026-05-18'),
        new OA\Property(property: 'owner', ref: '#/components/schemas/OwnerSummary'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'VaccineAlert',
    properties: [
        new OA\Property(property: 'vaccine_id', type: 'string', example: '01JVMR0M2T6FC7NG8R5GRM5AJV'),
        new OA\Property(property: 'type', type: 'string', example: 'Rabia'),
        new OA\Property(property: 'alert_type', type: 'string', example: 'VENCIDA'),
        new OA\Property(property: 'days_diff', type: 'integer', example: -3),
        new OA\Property(property: 'application_date', type: 'string', format: 'date', example: '2025-05-18'),
        new OA\Property(property: 'next_vaccine_date', type: 'string', format: 'date', example: '2026-05-18'),
        new OA\Property(property: 'months_validity', type: 'integer', example: 12),
        new OA\Property(property: 'pet', ref: '#/components/schemas/PetSummary'),
        new OA\Property(property: 'owner', ref: '#/components/schemas/OwnerSummary'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'StoreVeterinarianRequest',
    required: ['dni', 'name', 'email', 'password'],
    properties: [
        new OA\Property(property: 'dni', type: 'string', example: '12345678'),
        new OA\Property(property: 'name', type: 'string', example: 'Dra. María López'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'maria@veterinaria.com'),
        new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret123'),
        new OA\Property(property: 'phone', type: 'string', nullable: true, example: '999888777'),
        new OA\Property(property: 'address', type: 'string', nullable: true, example: 'Av. Central 123'),
        new OA\Property(property: 'latitude', type: 'number', format: 'float', nullable: true),
        new OA\Property(property: 'longitude', type: 'number', format: 'float', nullable: true),
        new OA\Property(property: 'active', type: 'boolean', example: true),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'StoreOwnerRequest',
    required: ['dni', 'name', 'email'],
    properties: [
        new OA\Property(property: 'dni', type: 'string', example: '87654321'),
        new OA\Property(property: 'name', type: 'string', example: 'Juan Pérez'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'juan@example.com'),
        new OA\Property(property: 'phone', type: 'string', nullable: true, example: '999777666'),
        new OA\Property(property: 'address', type: 'string', nullable: true, example: 'Jr. Lima 456'),
        new OA\Property(property: 'latitude', type: 'number', format: 'float', nullable: true),
        new OA\Property(property: 'longitude', type: 'number', format: 'float', nullable: true),
        new OA\Property(property: 'active', type: 'boolean', example: true),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'UpdateUserRequest',
    properties: [
        new OA\Property(property: 'dni', type: 'string', example: '87654321'),
        new OA\Property(property: 'name', type: 'string', example: 'Juan Pérez'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'juan@example.com'),
        new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret123'),
        new OA\Property(property: 'phone', type: 'string', nullable: true, example: '999777666'),
        new OA\Property(property: 'address', type: 'string', nullable: true, example: 'Jr. Lima 456'),
        new OA\Property(property: 'latitude', type: 'number', format: 'float', nullable: true),
        new OA\Property(property: 'longitude', type: 'number', format: 'float', nullable: true),
        new OA\Property(property: 'active', type: 'boolean', example: true),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'StorePetRequest',
    required: ['name', 'species', 'race', 'gender', 'temperament', 'reproductive_condition', 'color', 'years', 'months', 'status', 'user_id'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Firulais'),
        new OA\Property(property: 'species', type: 'string', example: 'Canino'),
        new OA\Property(property: 'race', type: 'string', example: 'Mestizo'),
        new OA\Property(property: 'gender', type: 'string', example: 'MACHO'),
        new OA\Property(property: 'temperament', type: 'string', example: 'Tranquilo'),
        new OA\Property(property: 'reproductive_condition', type: 'string', example: 'ENTERO'),
        new OA\Property(property: 'color', type: 'string', example: 'Marrón'),
        new OA\Property(property: 'years', type: 'integer', example: 3),
        new OA\Property(property: 'months', type: 'integer', example: 4),
        new OA\Property(property: 'status', type: 'string', example: 'ADOPTADO'),
        new OA\Property(property: 'user_id', type: 'string', example: '01JVMR0M2T6FC7NG8R5GRM5AJV'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'UpdatePetRequest',
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Firulais'),
        new OA\Property(property: 'temperament', type: 'string', example: 'Juguetón'),
        new OA\Property(property: 'reproductive_condition', type: 'string', example: 'CASTRADO'),
        new OA\Property(property: 'color', type: 'string', example: 'Negro'),
        new OA\Property(property: 'years', type: 'integer', example: 4),
        new OA\Property(property: 'months', type: 'integer', example: 1),
        new OA\Property(property: 'status', type: 'string', example: 'ADOPTADO'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'StoreVaccineRequest',
    required: ['type', 'aplication_date', 'months_validity', 'pet_id'],
    properties: [
        new OA\Property(property: 'type', type: 'string', example: 'Rabia'),
        new OA\Property(property: 'aplication_date', type: 'string', format: 'date', example: '2026-05-18'),
        new OA\Property(property: 'months_validity', type: 'integer', example: 12),
        new OA\Property(property: 'pet_id', type: 'string', example: '01JVMR0M2T6FC7NG8R5GRM5AJV'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'UpdateVaccineRequest',
    properties: [
        new OA\Property(property: 'type', type: 'string', example: 'Rabia'),
        new OA\Property(property: 'aplication_date', type: 'string', format: 'date', example: '2026-05-18'),
        new OA\Property(property: 'months_validity', type: 'integer', example: 12),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'StorePetImageRequest',
    required: ['pet_id', 'image'],
    properties: [
        new OA\Property(property: 'pet_id', type: 'string', example: '01JVMR0M2T6FC7NG8R5GRM5AJV'),
        new OA\Property(property: 'image', type: 'string', format: 'binary'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Foto frontal'),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ErrorResponse',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'No autenticado.'),
        new OA\Property(property: 'status', type: 'integer', example: 401),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ValidationErrorResponse',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'Los datos enviados no son válidos.'),
        new OA\Property(property: 'status', type: 'integer', example: 422),
        new OA\Property(
            property: 'errors',
            properties: [
                new OA\Property(
                    property: 'email',
                    type: 'array',
                    items: new OA\Items(type: 'string'),
                    example: ['El campo email es obligatorio.'],
                ),
                new OA\Property(
                    property: 'password',
                    type: 'array',
                    items: new OA\Items(type: 'string'),
                    example: ['El campo password es obligatorio.'],
                ),
            ],
            type: 'object',
        ),
    ],
    type: 'object',
)]
class OpenApiSpec {}

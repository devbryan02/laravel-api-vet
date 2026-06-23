# API Veterinaria - Documentación Completa
Sistema de gestión veterinaria con autenticación JWT, roles (ADMIN, VETERINARIAN, OWNER) y operaciones CRUD para mascotas, vacunas, imágenes y estadísticas.
---
## Base URL
```
http://localhost:8000/api
```
## Autenticación
La API usa **JWT (JSON Web Token)**. Todas las rutas protegidas requieren el header:
```
Authorization: Bearer {token}
```
### Roles del sistema
| Rol | Descripción |
|-----|-------------|
| `ADMIN` | Acceso total al sistema |
| `VETERINARIAN` | Gestión de mascotas, vacunas, dueños |
| `OWNER` | Dueño de mascotas (sin acceso a la API) |
---
## Formato de Respuestas
### Respuesta exitosa (lista paginada)
```json
{
  "data": [],
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 50,
    "last_page": 4,
    "has_more": true
  }
}
```
### Respuesta de error estándar
```json
{
  "success": false,
  "message": "Mensaje de error",
  "status": 422,
  "errors": []
}
```
### Códigos de error HTTP
| Código | Descripción |
|--------|-------------|
| 200 | Éxito |
| 201 | Creado exitosamente |
| 204 | Eliminado exitosamente |
| 400 | Solicitud incorrecta |
| 401 | No autenticado |
| 403 | Sin permisos |
| 404 | Recurso no encontrado |
| 405 | Método no permitido |
| 409 | Conflicto (registro relacionado o duplicado) |
| 422 | Error de validación |
| 500 | Error interno del servidor |
---
## Convenciones de IDs
Todos los IDs usan formato **ULID** (26 caracteres alfanuméricos).
---
# 📁 Autenticación
## `POST /auth/login`
Inicia sesión y obtiene un token JWT.
**Rol requerido:** Público
**Request:**
```json
{
  "email": "admin@example.com",
  "password": "password123"
}
```
**Response (200):**
```json
{
  "message": "Inicio de sesión exitoso.",
  "token": "eyJ0eXAiOiJKV1Qi...",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {
    "id": "01J...",
    "name": "Admin",
    "email": "admin@example.com",
    "role": "ADMIN"
  }
}
```
---
## `POST /auth/logout`
Invalida el token JWT actual.
**Rol requerido:** Autenticado
**Headers:**
```
Authorization: Bearer {token}
```
**Response (200):**
```json
{
  "message": "Sesion cerrada exitosamente."
}
```
---
## `GET /user`
Obtiene los datos del usuario autenticado.
**Rol requerido:** Autenticado
**Headers:**
```
Authorization: Bearer {token}
```
**Response (200):**
```json
{
  "data": {
    "id": "01J...",
    "dni": "12345678",
    "name": "Admin",
    "email": "admin@example.com",
    "phone": "999999999",
    "address": "Av. Principal 123",
    "latitude": "-12.046374",
    "longitude": "-77.042793",
    "active": true,
    "roles": ["ADMIN"],
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
}
```
---
# 👤 Usuarios
Todas las rutas de usuarios requieren autenticación.
## `GET /users/veterinarians`
Lista todos los veterinarios.
**Rol requerido:** `ADMIN`
**Query params:** `?page=1&per_page=15`
---
## `POST /users/veterinarians`
Crea un nuevo veterinario.
**Rol requerido:** `ADMIN`
**Request:**
```json
{
  "dni": "87654321",
  "name": "Dr. Juan Pérez",
  "email": "juan@example.com",
  "password": "password123",
  "phone": "999888777",
  "address": "Av. Secundaria 456",
  "latitude": "-12.050000",
  "longitude": "-77.040000",
  "active": true
}
```
---
## `GET /users/owners`
Lista paginada de dueños de mascotas.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Query params:** `?page=1&per_page=15`
---
## `GET /users/owners/search`
Busca dueños por DNI o nombre.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Query params:** `?search=Juan&page=1&per_page=15`
---
## `POST /users/owners`
Crea un nuevo dueño.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Request:**
```json
{
  "dni": "12345678",
  "name": "Carlos López",
  "email": "carlos@example.com",
  "phone": "999111222",
  "address": "Jr. Los Olivos 789",
  "latitude": "-12.060000",
  "longitude": "-77.050000",
  "active": true
}
```
**Validaciones especiales:** `dni` debe tener exactamente 8 dígitos numéricos.
---
## `GET /users/{user}`
Obtiene detalles de un usuario.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Response (200):**
```json
{
  "data": {
    "id": "01J...",
    "dni": "12345678",
    "name": "Carlos López",
    "email": "carlos@example.com",
    "phone": "999111222",
    "address": "Jr. Los Olivos 789",
    "latitude": "-12.060000",
    "longitude": "-77.050000",
    "active": true,
    "roles": ["OWNER"],
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
}
```
---
## `PUT|PATCH /users/{user}`
Actualiza un usuario.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Request (todos los campos son opcionales):**
```json
{
  "dni": "87654321",
  "name": "Carlos López Actualizado",
  "email": "carlosnuevo@example.com",
  "password": "nuevopassword123",
  "phone": "999333444",
  "address": "Av. Nueva 123",
  "latitude": "-12.070000",
  "longitude": "-77.060000",
  "active": false
}
```
---
## `DELETE /users/{user}`
Elimina un usuario.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Response:** `204 No Content`
---
# 🐾 Mascotas (Pets)
## `GET /pets`
Lista paginada de mascotas.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Query params:** `?page=1&per_page=15`
**Response (200):**
```json
{
  "data": [
    {
      "id": "01J...",
      "identifier": "MAACD00001",
      "name": "Firulais",
      "species": "Perro",
      "race": "Pastor Alemán",
      "gender": "MACHO",
      "color": "Negro y marrón",
      "temperament": "Amigable",
      "reproductive_condition": "CASTRADO",
      "age": "3 anos y 6 meses",
      "status": "ADOPTADO",
      "user": {
        "id": "01J...",
        "name": "Carlos López",
        "phone": "999111222"
      },
      "images": [
        {
          "id": "01J...",
          "path_url": "/storage/pets/01J...jpg",
          "pet_id": "01J..."
        }
      ]
    }
  ],
  "pagination": { ... }
}
```
---
## `POST /pets`
Crea una nueva mascota. El `identifier` se genera automáticamente (formato: `MAACD` + 5 dígitos secuenciales).
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Request:**
```json
{
  "name": "Firulais",
  "species": "Perro",
  "race": "Pastor Alemán",
  "gender": "MACHO",
  "temperament": "Amigable",
  "reproductive_condition": "CASTRADO",
  "color": "Negro y marrón",
  "years": 3,
  "months": 6,
  "status": "ADOPTADO",
  "user_id": "01J..."
}
```
**Géneros válidos:** `MACHO`, `HEMBRA`
**Condiciones reproductivas:** `ENTERO`, `CASTRADO`
**Estados válidos:** `ADOPTADO`, `EN ADOPCION`, `FALLECIDO`, `PERDIDO`
---
## `GET /pets/{pet}`
Obtiene detalles de una mascota con su dueño e imágenes.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
---
## `PUT|PATCH /pets/{pet}`
Actualiza una mascota.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Request (todos opcionales):**
```json
{
  "name": "Firulais Actualizado",
  "temperament": "Juguetón",
  "reproductive_condition": "ENTERO",
  "color": "Marrón claro",
  "years": 4,
  "months": 0,
  "status": "EN ADOPCION"
}
```
---
## `DELETE /pets/{pet}`
Elimina una mascota.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Response:** `204 No Content`
> **Nota:** Si la mascota tiene vacunas o imágenes asociadas, retorna error 409.
---
## `GET /pets/owner/{ownerId}`
Obtiene las mascotas de un dueño específico.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Query params:** `?page=1&per_page=15`
---
## `GET /pets/search`
Busca mascotas por nombre o por nombre/DNI del dueño.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Query params:** `?search=Firulais&page=1&per_page=15`
---
## `GET /pets/{pet}/vaccines`
Obtiene las vacunas de una mascota específica.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Query params:** `?page=1&per_page=15`
---
## `GET /verify/{identifier}`
Consulta pública de mascota por código identificador. **No requiere autenticación.**
**Response (200):**
```json
{
  "data": {
    "id": "01J...",
    "identifier": "MAACD00001",
    "name": "Firulais",
    "species": "Perro",
    "race": "Pastor Alemán",
    "gender": "MACHO",
    "color": "Negro y marrón",
    "temperament": "Amigable",
    "reproductive_condition": "CASTRADO",
    "age": "3 anos y 6 meses",
    "status": "ADOPTADO",
    "owner": {
      "id": "01J...",
      "name": "Carlos López",
      "phone": "999111222",
      "address": "Jr. Los Olivos 789",
      "dni": "12345678"
    },
    "images": [
      {
        "id": "01J...",
        "path_url": "/storage/pets/01J...jpg"
      }
    ],
    "vaccines": [
      {
        "id": "01J...",
        "type": "Rabia",
        "aplication_date": "2025-01-15",
        "months_validity": 12,
        "expiration_date": "2026-01-15",
        "next_vaccine_date": "2026-01-15"
      }
    ]
  }
}
```
---
# 🖼️ Imágenes (Pet Images)
## `GET /pets/storage/{path}*
Sirve archivos de imágenes almacenados. **No requiere autenticación.**
**Ejemplo:** `GET /api/pets/storage/pets/01J...jpg`
---
## `POST /pets/images`
Sube una imagen para una mascota.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Request (multipart/form-data):**
| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `pet_id` | string | Sí | ULID de la mascota |
| `image` | file | Sí | Archivo de imagen (jpeg, png, jpg, webp) - Máx 5MB |
| `description` | string | No | Descripción de la imagen |
**Response (201):**
```json
{
  "data": {
    "id": "01J...",
    "path_url": "/storage/pets/01J...jpg",
    "description": "Foto frontal",
    "pet_id": "01J..."
  }
}
```
---
## `DELETE /pets/images/{image}`
Elimina una imagen (base de datos y archivo físico).
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Response:** `204 No Content`
---
# 💉 Vacunas (Vaccines)
## `GET /vaccines`
Lista paginada de todas las vacunas.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Query params:** `?page=1&per_page=15`
**Response (200):**
```json
{
  "data": [
    {
      "id": "01J...",
      "type": "Rabia",
      "aplication_date": "2025-01-15",
      "months_validity": 12,
      "expiration_date": "2026-01-15",
      "next_vaccine_date": "2026-01-15",
      "pet": {
        "id": "01J...",
        "name": "Firulais"
      }
    }
  ],
  "pagination": { ... }
}
```
---
## `POST /vaccines`
Crea una vacuna. Las fechas `expiration_date` y `next_vaccine_date` se calculan automáticamente basadas en `aplication_date` + `months_validity`.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Request:**
```json
{
  "type": "Rabia",
  "aplication_date": "2025-01-15",
  "months_validity": 12,
  "pet_id": "01J..."
}
```
---
## `GET /vaccines/{vaccine}`
Obtiene detalles de una vacuna.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
---
## `PUT|PATCH /vaccines/{vaccine}`
Actualiza una vacuna. Si se cambia `aplication_date` o `months_validity`, las fechas se recalculan automáticamente.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Request (todos opcionales):**
```json
{
  "type": "Parvovirus",
  "aplication_date": "2025-06-01",
  "months_validity": 24
}
```
---
## `DELETE /vaccines/{vaccine}`
Elimina una vacuna.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Response:** `204 No Content`
---
# 📊 Estadísticas (Stats)
## `GET /stats/dashboard`
Resumen general del dashboard.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Response (200):**
```json
{
  "data": {
    "total_pets": 150,
    "total_owners": 80,
    "vaccinated_this_month": 12,
    "overdue_vaccines": 5,
    "upcoming_in_30_days": 8,
    "unvaccinated_count": 20
  }
}
```
---
## `GET /stats/vaccine-alerts`
Alertas de vacunas atrasadas y próximas a vencer.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Response (200):**
```json
{
  "data": [
    {
      "vaccine_id": "01J...",
      "type": "Rabia",
      "alert_type": "atrasada",
      "days_diff": -45,
      "application_date": "2025-01-15",
      "next_vaccine_date": "2026-01-15",
      "months_validity": 12,
      "pet": {
        "id": "01J...",
        "name": "Firulais",
        "species": "Perro",
        "race": "Pastor Alemán"
      },
      "owner": {
        "id": "01J...",
        "name": "Carlos López",
        "phone": "999111222"
      }
    }
  ]
}
```
> **alert_type:** `atrasada` (days_diff negativo) o `proxima` (days_diff positivo, dentro de 30 días)
---
## `GET /stats/unvaccinated`
Mascotas que no tienen ninguna vacuna registrada.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Response (200):**
```json
{
  "data": [
    {
      "id": "01J...",
      "name": "Max",
      "species": "Perro",
      "race": "Labrador",
      "status": "ADOPTADO",
      "registered_at": "2025-03-10T00:00:00.000000Z",
      "owner": {
        "id": "01J...",
        "name": "María García",
        "phone": "999555666"
      }
    }
  ]
}
```
---
## `GET /stats/monthly-activity`
Actividad mensual de los últimos 12 meses (nuevas mascotas y vacunas aplicadas).
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Response (200):**
```json
{
  "data": [
    {
      "month": "Enero 2025",
      "month_key": "2025-01",
      "new_pets": 5,
      "vaccines_applied": 8
    },
    {
      "month": "Febrero 2025",
      "month_key": "2025-02",
      "new_pets": 3,
      "vaccines_applied": 6
    }
  ]
}
```
---
## `GET /stats/species-distribution`
Distribución de mascotas por especie.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Response (200):**
```json
{
  "data": [
    {
      "species": "Perro",
      "total": 100,
      "percentage": 66.67
    },
    {
      "species": "Gato",
      "total": 50,
      "percentage": 33.33
    }
  ]
}
```
---
# 📋 Auditoría (Audits)
## `GET /audits`
Lista paginada de eventos de auditoría con filtros.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Query params:**
| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `page` | int | Número de página |
| `per_page` | int | Elementos por página |
| `entity_type` | string | Filtrar por tipo de entidad (pet, user, vaccine, image) |
| `action` | string | Filtrar por acción (CREATED, UPDATED, DELETED, RESTORED) |
| `user_id` | string | Filtrar por usuario que realizó la acción |
| `date_from` | date | Fecha inicial (Y-m-d) |
| `date_to` | date | Fecha final (Y-m-d) |
---
## `GET /audits/{audit}`
Obtiene detalle de un evento de auditoría.
**Rol requerido:** `VETERINARIAN`, `ADMIN`
**Response (200):**
```json
{
  "data": {
    "id": "01J...",
    "action": "UPDATED",
    "entity_type": "pet",
    "entity_id": "01J...",
    "payload": {
      "old": { "name": "Firulais", "status": "ADOPTADO" },
      "new": { "name": "Firulais Actualizado", "status": "EN ADOPCION" }
    },
    "ip_address": "127.0.0.1",
    "user_agent": "Mozilla/5.0...",
    "user": {
      "id": "01J...",
      "name": "Admin",
      "email": "admin@example.com"
    },
    "created_at": "2025-06-01T12:00:00.000000Z",
    "updated_at": "2025-06-01T12:00:00.000000Z"
  }
}
```
---
# 📌 Resumen de Rutas
## Públicas
| Método | Ruta | Descripción |
|--------|------|-------------|
| POST | `/api/auth/login` | Iniciar sesión |
| GET | `/api/pets/storage/{path}` | Servir archivos de imágenes |
| GET | `/api/verify/{identifier}` | Consultar mascota por código |
## Autenticadas (requieren `Authorization: Bearer {token}`)
| Método | Ruta | Roles | Descripción |
|--------|------|-------|-------------|
| POST | `/api/auth/logout` | * | Cerrar sesión |
| GET | `/api/user` | * | Datos del usuario autenticado |
## Usuarios (autenticadas)
| Método | Ruta | Roles | Descripción |
|--------|------|-------|-------------|
| GET | `/api/users/veterinarians` | ADMIN | Listar veterinarios |
| POST | `/api/users/veterinarians` | ADMIN | Crear veterinario |
| GET | `/api/users/owners` | VETERINARIAN, ADMIN | Listar dueños |
| GET | `/api/users/owners/search` | VETERINARIAN, ADMIN | Buscar dueños |
| POST | `/api/users/owners` | VETERINARIAN, ADMIN | Crear dueño |
| GET | `/api/users/{user}` | VETERINARIAN, ADMIN | Ver usuario |
| PUT/PATCH | `/api/users/{user}` | VETERINARIAN, ADMIN | Actualizar usuario |
| DELETE | `/api/users/{user}` | VETERINARIAN, ADMIN | Eliminar usuario |
## Mascotas (autenticadas)
| Método | Ruta | Roles | Descripción |
|--------|------|-------|-------------|
| GET | `/api/pets` | VETERINARIAN, ADMIN | Listar mascotas |
| POST | `/api/pets` | VETERINARIAN, ADMIN | Crear mascota |
| GET | `/api/pets/{pet}` | VETERINARIAN, ADMIN | Ver mascota |
| PUT/PATCH | `/api/pets/{pet}` | VETERINARIAN, ADMIN | Actualizar mascota |
| DELETE | `/api/pets/{pet}` | VETERINARIAN, ADMIN | Eliminar mascota |
| GET | `/api/pets/owner/{ownerId}` | VETERINARIAN, ADMIN | Mascotas por dueño |
| GET | `/api/pets/search` | VETERINARIAN, ADMIN | Buscar mascotas |
| GET | `/api/pets/{pet}/vaccines` | VETERINARIAN, ADMIN | Vacunas de una mascota |
## Imágenes (autenticadas)
| Método | Ruta | Roles | Descripción |
|--------|------|-------|-------------|
| POST | `/api/pets/images` | VETERINARIAN, ADMIN | Subir imagen |
| DELETE | `/api/pets/images/{image}` | VETERINARIAN, ADMIN | Eliminar imagen |
## Vacunas (autenticadas)
| Método | Ruta | Roles | Descripción |
|--------|------|-------|-------------|
| GET | `/api/vaccines` | VETERINARIAN, ADMIN | Listar vacunas |
| POST | `/api/vaccines` | VETERINARIAN, ADMIN | Crear vacuna |
| GET | `/api/vaccines/{vaccine}` | VETERINARIAN, ADMIN | Ver vacuna |
| PUT/PATCH | `/api/vaccines/{vaccine}` | VETERINARIAN, ADMIN | Actualizar vacuna |
| DELETE | `/api/vaccines/{vaccine}` | VETERINARIAN, ADMIN | Eliminar vacuna |
## Estadísticas (autenticadas)
| Método | Ruta | Roles | Descripción |
|--------|------|-------|-------------|
| GET | `/api/stats/dashboard` | VETERINARIAN, ADMIN | Resumen del dashboard |
| GET | `/api/stats/vaccine-alerts` | VETERINARIAN, ADMIN | Alertas de vacunas |
| GET | `/api/stats/unvaccinated` | VETERINARIAN, ADMIN | Mascotas sin vacunar |
| GET | `/api/stats/monthly-activity` | VETERINARIAN, ADMIN | Actividad mensual (12 meses) |
| GET | `/api/stats/species-distribution` | VETERINARIAN, ADMIN | Distribución por especie |
## Auditoría (autenticadas)
| Método | Ruta | Roles | Descripción |
|--------|------|-------|-------------|
| GET | `/api/audits` | VETERINARIAN, ADMIN | Listar auditoría (con filtros) |
| GET | `/api/audits/{audit}` | VETERINARIAN, ADMIN | Detalle de auditoría |
---
## Documentación Swagger
La API también cuenta con documentación interactiva Swagger/OpenAPI disponible en:
```
GET /api/documentation
```

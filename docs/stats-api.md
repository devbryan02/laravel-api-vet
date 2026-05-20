# API de Estadísticas (Stats)

## Información General

| Propiedad | Valor |
|---|---|
| **Base Path** | `/api/stats` |
| **Autenticación** | Bearer Token (`auth:api`) |
| **Roles permitidos** | `VETERINARIAN`, `ADMIN` |
| **Tag OpenAPI** | `Estadísticas` |

### Middleware

| Middleware | Descripción |
|---|---|
| `auth:api` | Requiere autenticación vía token bearer |
| `role:VETERINARIAN,ADMIN` | Solo usuarios con rol VETERINARIAN o ADMIN |

### Respuestas de Error Comunes

| Código | Descripción | Cuerpo |
|---|---|---|
| `401` | No autenticado | `{"message": "Unauthenticated."}` |
| `403` | No autorizado | `{"message": "No tienes permisos para acceder con este rol."}` |

---

## Endpoints

### 1. Dashboard Principal

**`GET /api/stats/dashboard`**

Números rápidos para el panel principal: totales de mascotas, propietarios, vacunas del mes, alertas.

#### Request

- **Query Params:** Ninguno
- **Body:** Ninguno

#### Response `200 OK`

```json
{
  "total_pets": 150,
  "total_owners": 80,
  "vaccinated_this_month": 25,
  "overdue_vaccines": 10,
  "upcoming_in_30_days": 15,
  "unvaccinated_count": 5
}
```

#### Campos del Resource (`DashboardStatsResource`)

| Campo | Tipo | Descripción | Origen |
|---|---|---|---|
| `total_pets` | `integer` | Total de mascotas registradas | `COUNT(*)` de `pets` |
| `total_owners` | `integer` | Total de propietarios | `COUNT` de usuarios con rol `OWNER` |
| `vaccinated_this_month` | `integer` | Mascotas vacunadas en el mes actual (únicas) | `COUNT DISTINCT pet_id` donde `aplication_date` está en el mes actual |
| `overdue_vaccines` | `integer` | Mascotas con vacuna vencida | Mascotas cuya última vacuna tiene `next_vaccine_date < hoy` |
| `upcoming_in_30_days` | `integer` | Vacunas próximas a vencer | Mascotas cuya última vacuna tiene `next_vaccine_date` entre hoy y hoy+30 días |
| `unvaccinated_count` | `integer` | Mascotas sin ninguna vacuna | `COUNT` de mascotas sin registros en `vaccines` |

#### Lógica del Query (`DashboardStatsQuery`)

- **Vacunas vencidas/próximas:** Se basa en la **última vacuna** de cada mascota (`MAX(id)` agrupado por `pet_id` donde `next_vaccine_date IS NOT NULL`).
- **Vacunadas este mes:** Cuenta mascotas distintas con `aplication_date` entre el inicio del mes actual y hoy.

---

### 2. Alertas de Vacunas

**`GET /api/stats/vaccine-alerts`**

Mascotas con vacuna vencida o próxima a vencer (en los próximos 30 días). Retorna las vacunas más recientes de cada mascota que cumplan la condición.

#### Request

- **Query Params:** Ninguno
- **Body:** Ninguno

#### Response `200 OK`

```json
{
  "data": [
    {
      "vaccine_id": "abc123def456...",
      "type": "Antirrábica",
      "alert_type": "atrasada",
      "days_diff": -15,
      "application_date": "2025-01-15",
      "next_vaccine_date": "2025-07-15",
      "months_validity": 12,
      "pet": {
        "id": "xyz789...",
        "name": "Firulais",
        "species": "Canino",
        "race": "Labrador"
      },
      "owner": {
        "id": "usr001...",
        "name": "Juan Perez",
        "phone": "555-1234"
      }
    }
  ],
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "...",
    "per_page": 15,
    "to": 1,
    "total": 1
  }
}
```

#### Campos del Resource (`VaccineAlertResource`)

| Campo | Tipo | Descripción |
|---|---|---|
| `vaccine_id` | `string` | ID de la vacuna (Ulid) |
| `type` | `string` | Tipo de vacuna (ej: "Antirrábica", "Quíntuple") |
| `alert_type` | `string` | `"atrasada"` si `next_vaccine_date < hoy`, `"próxima"` si está en los próximos 30 días |
| `days_diff` | `integer` | Diferencia en días respecto a hoy. **Negativo** = atrasada, **Positivo** = días restantes |
| `application_date` | `string` | Fecha de aplicación de la vacuna (`YYYY-MM-DD`) |
| `next_vaccine_date` | `string` | Fecha de próxima vacuna (`YYYY-MM-DD`) |
| `months_validity` | `integer` | Meses de validez de la vacuna |
| `pet` | `object` | Datos de la mascota |
| `pet.id` | `string` | ID de la mascota (Ulid) |
| `pet.name` | `string` | Nombre de la mascota |
| `pet.species` | `string` | Especie (ej: "Canino", "Felino") |
| `pet.race` | `string` | Raza |
| `owner` | `object` | Datos del propietario |
| `owner.id` | `string` | ID del propietario (Ulid) |
| `owner.name` | `string` | Nombre del propietario |
| `owner.phone` | `string` | Teléfono del propietario |

#### Lógica del Query (`VaccineAlertsQuery`)

1. Obtiene los IDs de las **últimas vacunas** por mascota (`MAX(id)` donde `next_vaccine_date IS NOT NULL`, agrupado por `pet_id`).
2. Filtra vacunas donde `next_vaccine_date < hoy` (atrasadas) **O** `next_vaccine_date` entre `hoy` y `hoy + 30 días` (próximas).
3. Ordena por `next_vaccine_date ASC` (las más urgentes primero).
4. Eager load: `pet` → `user` (propietario).

---

### 3. Mascotas sin Vacunas

**`GET /api/stats/unvaccinated`**

Lista de mascotas que no tienen ninguna vacuna registrada.

#### Request

- **Query Params:** Ninguno
- **Body:** Ninguno

#### Response `200 OK`

```json
{
  "data": [
    {
      "id": "pet001abc...",
      "name": "Michi",
      "species": "Felino",
      "race": "Siamés",
      "status": "Activo",
      "registered_at": "2025-03-10",
      "owner": {
        "id": "usr002def...",
        "name": "Maria Lopez",
        "phone": "555-5678"
      }
    }
  ],
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "...",
    "per_page": 15,
    "to": 1,
    "total": 1
  }
}
```

#### Campos del Resource (`UnvaccinatedPetResource`)

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | `string` | ID de la mascota (Ulid) |
| `name` | `string` | Nombre de la mascota |
| `species` | `string` | Especie |
| `race` | `string` | Raza |
| `status` | `string` | Estado de la mascota |
| `registered_at` | `string` | Fecha de registro (`YYYY-MM-DD`) |
| `owner` | `object` | Datos del propietario |
| `owner.id` | `string` | ID del propietario (Ulid) |
| `owner.name` | `string` | Nombre del propietario |
| `owner.phone` | `string` | Teléfono del propietario |

#### Lógica del Query (`UnvaccinatedPetsQuery`)

- `Pet::doesntHave('vaccines')` — solo mascotas sin registros en la tabla `vaccines`.
- Ordena por `created_at DESC` (más recientes primero).
- Eager load: `user` (id, name, phone).

---

### 4. Actividad Mensual

**`GET /api/stats/monthly-activity`**

Mascotas nuevas y vacunas aplicadas por mes, últimos 12 meses.

#### Request

- **Query Params:** Ninguno
- **Body:** Ninguno

#### Response `200 OK`

```json
{
  "data": [
    {
      "month": "Jun 2025",
      "month_key": "2025-06",
      "new_pets": 12,
      "vaccines_applied": 45
    },
    {
      "month": "Jul 2025",
      "month_key": "2025-07",
      "new_pets": 8,
      "vaccines_applied": 30
    },
    {
      "month": "Ago 2025",
      "month_key": "2025-08",
      "new_pets": 15,
      "vaccines_applied": 52
    }
  ]
}
```

#### Campos del Resource (`MonthlyActivityResource`)

| Campo | Tipo | Descripción |
|---|---|---|
| `month` | `string` | Nombre del mes abreviado + año (ej: "Jun 2025", formato `translatedFormat('M Y')`) |
| `month_key` | `string` | Clave del mes en formato `YYYY-MM` (ej: "2025-06") |
| `new_pets` | `integer` | Cantidad de mascotas registradas en ese mes |
| `vaccines_applied` | `integer` | Cantidad de vacunas aplicadas en ese mes |

#### Lógica del Query (`MonthlyActivityQuery`)

- Itera los **últimos 12 meses** (desde `now - 11 months` hasta `now`).
- Para cada mes:
  - `new_pets`: `COUNT` de pets donde `YEAR(created_at) = X AND MONTH(created_at) = Y`.
  - `vaccines_applied`: `COUNT` de vacunas donde `YEAR(aplication_date) = X AND MONTH(aplication_date) = Y`.
- El array está ordenado cronológicamente (del más antiguo al más reciente).

---

### 5. Distribución por Especie

**`GET /api/stats/species-distribution`**

Distribución de mascotas por especie con conteo y porcentaje.

#### Request

- **Query Params:** Ninguno
- **Body:** Ninguno

#### Response `200 OK`

```json
{
  "data": [
    {
      "species": "Canino",
      "total": 90,
      "percentage": 60.0
    },
    {
      "species": "Felino",
      "total": 50,
      "percentage": 33.3
    },
    {
      "species": "Ave",
      "total": 10,
      "percentage": 6.7
    }
  ],
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "...",
    "per_page": 15,
    "to": 3,
    "total": 3
  }
}
```

#### Campos del Resource (`SpeciesDistributionResource`)

| Campo | Tipo | Descripción |
|---|---|---|
| `species` | `string` | Nombre de la especie |
| `total` | `integer` | Cantidad de mascotas de esa especie |
| `percentage` | `float` | Porcentaje del total (redondeado a 1 decimal) |

#### Lógica del Query (`SpeciesDistributionQuery`)

- `COUNT(*)` total de mascotas.
- `GROUP BY species` con `COUNT(*) as total`.
- `percentage = (total_species / total_pets) * 100`, redondeado a 1 decimal. Si `total_pets = 0`, porcentaje = `0`.
- Ordena por `total DESC` (especies más comunes primero).

---

## Arquitectura

```
Ruta (routes/api.php)
  │
  ▼
StatsController
  ├── dashboard()         → StatsService::getDashboardStats()         → DashboardStatsQuery      → DashboardStatsResource
  ├── vaccineAlerts()     → StatsService::getVaccineAlerts()          → VaccineAlertsQuery       → VaccineAlertResource
  ├── unvaccinated()      → StatsService::getUnvaccinatedPets()       → UnvaccinatedPetsQuery    → UnvaccinatedPetResource
  ├── monthlyActivity()   → StatsService::getMonthlyActivity()        → MonthlyActivityQuery     → MonthlyActivityResource
  └── speciesDistribution() → StatsService::getSpeciesDistribution()  → SpeciesDistributionQuery → SpeciesDistributionResource
```

## Archivos de la Feature

| Tipo | Archivo |
|---|---|
| Controller | `app/Features/Stats/Controllers/StatsController.php` |
| Service | `app/Features/Stats/Services/StatsService.php` |
| Query | `app/Features/Stats/Queries/DashboardStatsQuery.php` |
| Query | `app/Features/Stats/Queries/VaccineAlertsQuery.php` |
| Query | `app/Features/Stats/Queries/UnvaccinatedPetsQuery.php` |
| Query | `app/Features/Stats/Queries/MonthlyActivityQuery.php` |
| Query | `app/Features/Stats/Queries/SpeciesDistributionQuery.php` |
| Resource | `app/Features/Stats/Resources/DashboardStatsResource.php` |
| Resource | `app/Features/Stats/Resources/VaccineAlertResource.php` |
| Resource | `app/Features/Stats/Resources/UnvaccinatedPetResource.php` |
| Resource | `app/Features/Stats/Resources/MonthlyActivityResource.php` |
| Resource | `app/Features/Stats/Resources/SpeciesDistributionResource.php` |

## Resumen de Endpoints

| Método | Path | Descripción | Response Type |
|---|---|---|---|
| `GET` | `/api/stats/dashboard` | Resumen numérico del dashboard | Objeto único |
| `GET` | `/api/stats/vaccine-alerts` | Alertas de vacunas (vencidas/próximas) | Colección paginada |
| `GET` | `/api/stats/unvaccinated` | Mascotas sin vacunas | Colección paginada |
| `GET` | `/api/stats/monthly-activity` | Actividad mensual (12 meses) | Colección (sin paginación) |
| `GET` | `/api/stats/species-distribution` | Distribución por especie | Colección paginada |

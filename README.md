# SysBase — Backend (Laravel 11)

Base reutilizable para nuevos sistemas: API REST con autenticación Sanctum (cookie), usuarios, roles y permisos (Spatie), menú dinámico, perfil, notificaciones (correo + WhatsApp Cloud API) y herramientas de desarrollo.

## Módulos incluidos

| Módulo | Rutas |
|---|---|
| Auth (Sanctum) | `routes/auth.php` |
| Usuarios, roles, permisos | `routes/rutas_users.php` |
| Menú dinámico, perfil, dashboard | `routes/api.php` |
| Departamentos/municipios de Guatemala, notificaciones | `routes/configuracion.php` (prefijo `api/configuracion`) |
| Sección Dev (logs, colas, Redis, info) | `routes/dev.php`, `routes/log_viewer.php` |

## Requisitos

- PHP 8.2+ (probado con 8.3 NTS x64), Composer
- SQL Server con autenticación mixta (Windows + SQL) y TCP/IP habilitado (puerto 1433)
- Microsoft ODBC Driver 17 o 18 para SQL Server
- Extensiones PHP `sqlsrv` y `pdo_sqlsrv`

### Drivers de SQL Server para PHP (Windows / Laragon)

1. Descargar el zip de Windows de [microsoft/msphpsql](https://github.com/microsoft/msphpsql/releases).
   Para PHP 8.3 usar la versión **5.13.x** (la 5.14 ya no incluye PHP 8.3).
2. Copiar `php_sqlsrv_83_nts_x64.dll` y `php_pdo_sqlsrv_83_nts_x64.dll` a la carpeta `ext` de PHP.
3. Agregar al final de `php.ini`:
   ```ini
   extension=php_sqlsrv_83_nts_x64.dll
   extension=php_pdo_sqlsrv_83_nts_x64.dll
   ```
4. Verificar con `php -m | findstr sqlsrv` (deben salir `sqlsrv` y `pdo_sqlsrv`).

### Base de datos y login

Crear la base y un login SQL (cambiar `TuPassword`):

```sql
CREATE DATABASE dbreclutamiento;
CREATE LOGIN reclutamiento WITH PASSWORD = 'TuPassword', CHECK_POLICY = OFF;
GO
USE dbreclutamiento;
CREATE USER reclutamiento FOR LOGIN reclutamiento;
ALTER ROLE db_owner ADD MEMBER reclutamiento;
```

Si el servidor está en modo solo Windows, activar **Propiedades > Seguridad > Modo de autenticación
de SQL Server y Windows** en SSMS y reiniciar el servicio.

## Puesta en marcha

```bash
composer install
cp .env.example .env && php artisan key:generate
# editar .env: DB_USERNAME y DB_PASSWORD
php artisan migrate --seed
php artisan storage:link
php artisan serve --host=localhost --port=8000
```

Usuario inicial: `admin` / `12345` (rol SuperAdministrador). El login usa el **username**, no el correo.

### Variables `.env` importantes

| Variable | Valor local | Para qué |
|---|---|---|
| `DB_*` | ver `.env.example` | Conexión a SQL Server |
| `DB_ENCRYPT` / `DB_TRUST_SERVER_CERTIFICATE` | `no` / `true` | Conexión local sin certificado (en producción usar `yes` con certificado válido) |
| `APP_URL` | `http://localhost:8000` | Genera las URL públicas (p. ej. avatares); debe coincidir con el puerto real |
| `FRONTEND_URL` | `http://localhost:3000` | Origen permitido por CORS |
| `SANCTUM_STATEFUL_DOMAINS` | `localhost:3000` | Dominios del front que usan cookies de sesión |

## Fase 2 — Algoritmos numéricos

Tres ejercicios en HTML, CSS, JavaScript, en `public/algoritmos/`.
Con el servidor del backend levantado, la página de inicio es:

- `php artisan serve`: <http://localhost:8000/algoritmos/index.html>
- Laragon (host virtual automático): <http://reclutamientoback.test/algoritmos/index.html>

| Ejercicio | Página | Capturas |
|---|---|---|
| 1. Número par o impar | `public/algoritmos/par-impar.html` | [`docs/Algoritmos/Ejercicio#1`](docs/Algoritmos/Ejercicio%231) |
| 2. Números primos en un rango | `public/algoritmos/primos.html` | [`docs/Algoritmos/Ejercicio#2`](docs/Algoritmos/Ejercicio%232) |
| 3. Suma de pares de Fibonacci | `public/algoritmos/fibonacci.html` | [`docs/Algoritmos/Ejercicio#3`](docs/Algoritmos/Ejercicio%233) |


## Fase 3 — Modelado (ERD MVP)

Caso elegido: **A — Sistema de Reclutamiento y Selección de Personal** (Cooperativa Guayacán).

Diagrama ER: [`docs/ER/ER-RECLUTAMIENTO-SQLSERVER.png`](docs/ER/ER-RECLUTAMIENTO-SQLSERVER.png)

| Tabla | Descripción |
|---|---|
| `unidades` | Unidades o departamentos a los que pertenece una vacante |
| `modalidades`, `estados_vacante`, `estados_postulacion`, `fuentes_reclutamiento`, `tipos_entrevista`, `resultados_entrevista` | Catálogos |
| `vacantes` | Puesto, descripción, rango salarial, ubicación, estado, unidad y modalidad |
| `candidatos` | Nombres y apellidos, correo (único), teléfono, fecha de nacimiento, expectativa salarial |
| `postulaciones` | Candidato + vacante (par único), fecha, estado y fuente de reclutamiento |
| `postulacion_historial` | Cada cambio de estado de una postulación (las postulaciones no se eliminan, solo cambian de estado) |
| `entrevistas` | Entrevistas de una postulación: fecha y hora, tipo, entrevistador, resultado y observaciones |

## Fase 4 — Mini sistema (Opción B: API Laravel + front Nuxt)

- **Backend:** este repositorio (Laravel 11, SQL Server, Sanctum por cookie).
- **Frontend:** repositorio `ReclutamientoFront` (Nuxt 3 + Vuetify), carpeta hermana en `C:\laragon\www\`.

### Ejecutarlo en Laragon

1. Colocar ambos proyectos en `C:\laragon\www\`:
   `ReclutamientoBack` y `ReclutamientoFront`.
2. Iniciar **Laragon** (Nginx/Apache + PHP 8.3). Laragon crea solo el host virtual
   `http://reclutamientoback.test` apuntando a `ReclutamientoBack/public`
   (si no aparece: menú de Laragon > *Preferences* > *General* > *Auto virtual hosts*, y reiniciar).
3. Tener SQL Server con la base y el login creados (ver [Base de datos y login](#base-de-datos-y-login)) y
   las extensiones `sqlsrv` / `pdo_sqlsrv` activas.
4. Backend, desde `ReclutamientoBack`:
   ```bash
   composer install
   cp .env.example .env && php artisan key:generate
   ```
   En el `.env` configurar `DB_USERNAME`, `DB_PASSWORD` y estas URLs:
   ```env
   APP_URL=http://reclutamientoback.test
   FRONTEND_URL=http://localhost:3000
   SANCTUM_STATEFUL_DOMAINS=localhost:3000
   SESSION_DOMAIN=localhost
   ```
   Luego:
   ```bash
   php artisan migrate --seed
   php artisan storage:link
   ```
5. Frontend, desde `ReclutamientoFront`. En su `.env`:
   ```env
   NUXT_PUBLIC_API_BASE_URL=http://localhost:3000
   BACKEND_URL=http://reclutamientoback.test
   ```
   Luego:
   ```bash
   npm install
   npm run dev
   ```
6. Abrir <http://localhost:3000>.

> **Por qué `NUXT_PUBLIC_API_BASE_URL` apunta al propio front:** Nuxt reenvía `/api/**` y `/sanctum/**`
> a `BACKEND_URL` (ver `routeRules` en `nuxt.config.ts`). Así el navegador solo habla con `localhost:3000`
> y las cookies de Sanctum funcionan aunque el backend esté en `reclutamientoback.test`.
> Si prefieres no usar el host virtual, también sirve `php artisan serve` con
> `APP_URL=http://localhost:8000` y `BACKEND_URL=http://localhost:8000`.

### Usuario de prueba

| Campo | Valor |
|---|---|
| Usuario | `admin` |
| Contraseña | `12345` |
| Rol | SuperAdministrador |

El login usa el **username**, no el correo.

### Datos de ejemplo (seeders)

`php artisan migrate --seed` carga los catálogos (modalidades, estados, fuentes, tipos y resultados de
entrevista), 4 unidades, 4 candidatos, 4 vacantes, 4 postulaciones con su historial y 2 entrevistas.
Los seeders usan `updateOrCreate`, así que se pueden volver a correr sin duplicar datos:

```bash
php artisan db:seed
```

## Crear un módulo nuevo

```bash
php artisan make:api {tabla} {Modelo}   # genera modelo, controlador, requests y rutas
```

Luego, en el frontend: `npm run generarCrudV3`. Agregá la opción de menú y sus permisos en
`database/seeders/MenuOpciones/` y `database/seeders/AdministracionSistema/PermisosSeeder.php`.

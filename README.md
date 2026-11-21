# SysBase — Backend (Laravel 11)

Base reutilizable para nuevos sistemas: API REST con autenticación Sanctum (cookie), usuarios, roles y permisos (Spatie), menú dinámico, perfil, notificaciones (correo + WhatsApp Cloud API) y herramientas de desarrollo.

## Módulos incluidos

| Módulo | Rutas |
|---|---|
| Auth (Sanctum) | `routes/auth.php` |
| Usuarios, roles, permisos | `routes/rutas_users.php` |
| Menú dinámico, perfil, dashboard | `routes/api.php` |
| Departamentos/municipios de Guatemala, notificaciones | `routes/configuracion.php` (prefijo `api/configuracion`) |
| Webhook WhatsApp | `api/whatsapp/webhook` (fuera de `auth:sanctum`) |
| Sección Dev (logs, colas, Redis, info) | `routes/dev.php`, `routes/log_viewer.php` |

## Puesta en marcha

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
php artisan storage:link
composer dev
```

Usuario inicial: `admin` / `12345` (rol SuperAdministrador).

## Crear un módulo nuevo

```bash
php artisan make:api {tabla} {Modelo}   # genera modelo, controlador, requests y rutas
```

Luego, en el frontend: `npm run generarCrudV3`. Agregá la opción de menú y sus permisos en
`database/seeders/MenuOpciones/` y `database/seeders/AdministracionSistema/PermisosSeeder.php`.

## Notificaciones

`App\Services\NotificacionService` envía correo (`enviarEmail`, `enviarMailable`) y WhatsApp
(`enviarWhatsapp`, `enviarWhatsappPlantilla`) y deja cada envío en la tabla `notificaciones`.
Para ligar un envío a un registro de tu módulo pasá `['notificable' => $modelo, 'origen' => 'mi_modulo']`.

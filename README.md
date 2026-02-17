# My Portfolio

Aplicación web en PHP (MVC simple) para publicar proyectos personales y administrarlos desde un panel privado.

## Objetivo

Mostrar proyectos de forma pública y permitir su gestión interna (proyectos, tecnologías y administradores) con autenticación de usuarios admin.

## Stack y arquitectura

- **Backend:** PHP 8+
- **Persistencia:** MySQL/MariaDB
- **Frontend:** HTML + CSS + JavaScript vanilla
- **Arquitectura:** MVC simple (`app/controllers`, `app/models`, `app/views`)

Estructura principal:

- `public/`: punto de entrada (`index.php`) y assets públicos.
- `core/`: utilidades base (router, entorno, DB, auth, csrf, renderizado de vistas).
- `app/controllers/`: controladores por dominio.
- `app/models/`: acceso a datos y lógica de persistencia.
- `app/views/`: plantillas de UI.
- `database/migrations/`: cambios de esquema incrementales.
- `storage/`: contenido editable y archivos de soporte en runtime.

## Funcionalidades actuales

### Zona pública

- Home del portafolio.
- Listado de proyectos con filtro por estado.
- Detalle de proyecto con tecnologías asociadas.
- Apertura de enlace de proyecto (`/projects/open/{id}`):
  - redirige si la URL es válida;
  - muestra vista de “no disponible” si está pendiente o sin URL.
- Sección “Sobre mí / Contacto”.
- Integración del miniproyecto **Ahorcado** en `/hangman`.

### Zona administrativa

- Login de administrador con usuario o email.
- Protección por sesión (`Auth::requireLogin`) en rutas privadas.
- Protección CSRF en formularios sensibles.
- Límite de intentos de login por sesión/IP (bloqueo temporal).
- CRUD de proyectos (crear, editar, archivar/restaurar).
- CRUD de tecnologías y activación/desactivación (`is_active`).
- CRUD de administradores con estado activo/inactivo.
- Registro de auditoría de acciones administrativas (si existe `admin_audit_logs`).
- Recuperación de contraseña por token (si existe `admin_password_resets`).

## Configuración de entorno

La app lee variables desde `.env` y, si no existe, desde `.emp`.

1. Copia `.emp.example` a `.env` (o `.emp`).
2. Completa los valores según tu entorno local.
3. Asegúrate de no versionar archivos de entorno con información sensible.

Variables utilizadas:

- `DB_HOST`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `DB_CHARSET` (opcional, por defecto `utf8mb4`)

## Ejecución local

1. Levanta un servidor web apuntando a `public/`.
2. Configura la base de datos en `.env`/`.emp`.
3. Crea las tablas base requeridas para el proyecto.
4. Aplica las migraciones de `database/migrations/` según corresponda a tu estado actual.
5. Abre la app en tu host local.

## Despliegue en hosting compartido (`public_html`)

Si tu proveedor usa `public_html` (en lugar de `public/` como document root configurable), **no necesitas cambiar controladores ni rutas**.

Usa esta estructura en el servidor:

- `~/my-portfolio/` → código de la app (`app`, `core`, `storage`, etc.).
- `~/public_html/` → contenido público (equivalente a la carpeta `public` de este repo).

Pasos recomendados:

1. Sube el proyecto completo a una carpeta privada, por ejemplo `~/my-portfolio`.
2. Copia el contenido de `public/` dentro de `~/public_html/`.
3. En `~/public_html/index.php`, ajusta los `require_once` para apuntar al proyecto real.

Ejemplo (si el proyecto quedó en `~/my-portfolio`):

```php
require_once __DIR__ . '/../my-portfolio/core/Env.php';
require_once __DIR__ . '/../my-portfolio/core/Database.php';
require_once __DIR__ . '/../my-portfolio/core/Auth.php';
require_once __DIR__ . '/../my-portfolio/core/Csrf.php';

Env::load(__DIR__ . '/../my-portfolio');

require_once __DIR__ . '/../my-portfolio/core/Router.php';
```

4. Asegúrate de copiar también `public/.htaccess` a `~/public_html/.htaccess`.
5. Crea tu `.env` en la carpeta privada del proyecto (`~/my-portfolio/.env`) con los datos de BD.

Con esto, las URLs como `/projects`, `/auth/login`, etc. seguirán funcionando igual bajo tu dominio, porque el sitio público ya estará sirviendo desde `public_html`.

> Nota: este repositorio no incluye contraseñas, credenciales reales ni secretos operativos.

## Base de datos

Entidades principales utilizadas por la aplicación:

- `projects`
- `technologies`
- `project_technology`
- `admin_users`

Entidades opcionales (habilitan features adicionales):

- `admin_audit_logs` (auditoría de administración)
- `admin_password_resets` (recuperación de contraseña por token)

Migraciones incluidas:

- `20260216_add_project_url.sql`
- `20260216_add_technology_is_active.sql`

## Contenido editable de “Sobre mí / Contacto”

La sección “Sobre mí” se persiste en:

- `storage/site_content.json`

Si el archivo no existe o no es legible, la app usa valores por defecto definidos en `SiteContentModel`.

## Seguridad y buenas prácticas

- No subas `.env` ni `.emp` al repositorio.
- Usa contraseñas robustas para usuarios administradores.
- Mantén permisos adecuados en `storage/` para escritura controlada.
- Revisa y aplica migraciones en entornos no productivos antes de producción.

## Licencia

Este proyecto se distribuye bajo la licencia incluida en `LICENSE`.

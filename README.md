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
- `APP_URL` (URL pública base para construir enlace de reset)
- `MAIL_TRANSPORT` (`mail` o `smtp`)
- `MAIL_FROM`
- `MAIL_FROM_NAME` (opcional)
- `SMTP_HOST` (si `MAIL_TRANSPORT=smtp`)
- `SMTP_PORT` (si `MAIL_TRANSPORT=smtp`)
- `SMTP_ENCRYPTION` (`tls`, `ssl` o vacío; si `MAIL_TRANSPORT=smtp`)
- `SMTP_USERNAME` (si `MAIL_TRANSPORT=smtp`)
- `SMTP_PASSWORD` (si `MAIL_TRANSPORT=smtp`)
- `SMTP_HELO_DOMAIN` (opcional, por defecto `localhost`)

### Recuperación de contraseña por correo en hosting

En local puede funcionar con logs o con `mail()` según tu entorno, pero en hosting compartido casi siempre necesitas SMTP autenticado.

Desde esta versión, el flujo `/auth/forgot`:

1. Genera y guarda token en `admin_password_resets`.
2. Envía correo real al `email` del administrador usando `MAIL_TRANSPORT`.
3. Si el envío falla, muestra error en pantalla (no “éxito falso”).

Checklist rápido para producción:

1. Verifica que el admin tenga correo válido en `admin_users.email`.
2. Configura `.env` con SMTP real de tu proveedor (Hostinger/cPanel/Workspace).
3. Usa `APP_URL=https://tu-dominio.com` para que el enlace sea correcto.
4. Revisa el log de PHP si aparece error SMTP (credenciales, TLS, puerto, remitente).
5. Asegura que `MAIL_FROM` pertenezca a tu dominio y exista como buzón/cuenta SMTP.

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

Si en tu hosting la home carga pero rutas como `/admins` o `/projects` siguen mostrando inicio, revisa que en `public_html/.htaccess` tengas **exactamente** esta base:

```apache
Options -MultiViews
DirectoryIndex index.php

RewriteEngine On
RewriteBase /

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
```

Además, el router ahora incluye un fallback por `REQUEST_URI`, por lo que también funcionará aunque tu proveedor limite la reescritura del parámetro `url`.

Con esto, las URLs como `/projects`, `/auth/login`, etc. seguirán funcionando igual bajo tu dominio, porque el sitio público ya estará sirviendo desde `public_html`.

> Nota: este repositorio no incluye contraseñas, credenciales reales ni secretos operativos.


## Flujo recomendado para producción + local (Laragon)

Para evitar que `public_html` se desactualice, el proyecto ahora incluye el script:

- `scripts/deploy-hostinger.sh`

### ¿Qué hace este script?

1. Copia todo lo público de `my-portfolio/public` (CSS, JS, IMG, etc.) hacia `public_html`.
2. Deja fijo `public_html/index.php` como “puente” hacia `my-portfolio/public/index.php`.
3. Deja fijo `public_html/.htaccess` con reglas de rutas.

Así, `public_html` siempre queda igual que tu proyecto real al hacer deploy.

### Producción (GitHub Actions)

El workflow ya lo ejecuta automáticamente después de `git pull`.

### Local en Laragon (replicar hosting)

Si en tu PC tienes esta estructura:

- `C:/laragon/www/my-portfolio`
- `C:/laragon/www/public_html`

puedes sincronizar manualmente con:

```bash
cd C:/laragon/www/my-portfolio
bash scripts/deploy-hostinger.sh --public-html-dir "C:/laragon/www/public_html"
```

Después, en Laragon, usa como Document Root la carpeta `public_html`.

### Alternativa local más simple (sin replicar hosting)

Para desarrollo diario también puedes apuntar Laragon directo a:

- `C:/laragon/www/my-portfolio/public`

y te ahorras la capa `public_html`. Esta opción suele ser más simple para programar.

Cuando quieras validar exactamente “como en producción”, corres el script y pruebas con `public_html`.

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

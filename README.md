# My Portfolio

Aplicación web en PHP (MVC simple) para publicar proyectos y administrarlos desde un panel privado.

## Estado del proyecto

Proyecto **activo y mantenido**. Esta versión limpia documentación antigua, estandariza la configuración con `.env` y conserva compatibilidad legacy con `.emp` como fallback automático en runtime.

## Stack

- PHP 8+
- MySQL/MariaDB
- HTML, CSS y JavaScript vanilla
- Arquitectura MVC simple

## Estructura principal

- `public/`: entrypoint web y assets públicos.
- `core/`: utilidades base (router, entorno, DB, auth, csrf, mail, render).
- `app/controllers/`, `app/models/`, `app/views/`: capa MVC.
- `database/migrations/`: migraciones SQL incrementales.
- `storage/`: contenido editable en runtime.
- `scripts/deploy-hostinger.sh`: sincronización para despliegue en `public_html`.

## Funcionalidades

### Zona pública

- Home del portafolio.
- Listado y detalle de proyectos.
- Apertura de URL de proyecto (`/projects/open/{id}`) con fallback a vista de no disponible.
- Sección “Sobre mí / Contacto” editable.
- Integración del miniproyecto **Ahorcado** (`/hangman`).

### Zona administrativa

- Autenticación por usuario o email.
- Protección de rutas privadas por sesión.
- CSRF en formularios sensibles.
- Límite de intentos de login por sesión/IP.
- CRUD de proyectos (incluye archivar/restaurar).
- CRUD de tecnologías (incluye activación/desactivación si existe `is_active`).
- CRUD de administradores (activo/inactivo).
- Auditoría administrativa (si existe `admin_audit_logs`).
- Recuperación de contraseña por token (si existe `admin_password_resets`).

## Configuración de entorno

1. Copia `.env.example` a `.env`.
2. Completa valores reales.
3. No versiones archivos con secretos (`.env`, claves, etc.).

> Compatibilidad legacy: `core/Env.php` intenta cargar `.env` y, si no existe, usa `.emp`.

Variables principales:

- `DB_HOST`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `DB_CHARSET` (opcional, default `utf8mb4`)
- `APP_URL`
- `MAIL_TRANSPORT` (`mail` o `smtp`)
- `MAIL_FROM`
- `MAIL_FROM_NAME` (opcional)
- `SMTP_HOST`, `SMTP_PORT`, `SMTP_ENCRYPTION`, `SMTP_USERNAME`, `SMTP_PASSWORD`, `SMTP_HELO_DOMAIN` (cuando `MAIL_TRANSPORT=smtp`)

## Ejecución local

1. Configura virtual host/document root apuntando a `public/`.
2. Crea `.env` a partir de `.env.example`.
3. Prepara la base de datos y tablas base.
4. Aplica migraciones de `database/migrations/` según tu estado.
5. Inicia el servidor y navega a la URL local.

## Base de datos

Tablas principales:

- `projects`
- `technologies`
- `project_technology`
- `admin_users`

Tablas opcionales:

- `admin_audit_logs`
- `admin_password_resets`

Migraciones incluidas:

- `20260216_add_project_url.sql`
- `20260216_add_technology_is_active.sql`

## Despliegue en hosting con `public_html`

Si tu hosting expone `public_html`, usa el script:

```bash
bash scripts/deploy-hostinger.sh
```

Este script:

1. Sincroniza `public/` hacia `public_html`.
2. Reemplaza `index.php` y `.htaccess` en `public_html` con plantillas versionadas (`deploy/hostinger/public_html/`).

También puedes indicar una ruta custom:

```bash
bash scripts/deploy-hostinger.sh --public-html-dir "C:/laragon/www/public_html"
```

## Seguridad y buenas prácticas

- No subas secretos al repositorio.
- Usa contraseñas robustas para cuentas admin.
- Mantén permisos de escritura controlados en `storage/`.
- Valida migraciones en staging antes de producción.

## Licencia

MIT, ver archivo `LICENSE`.

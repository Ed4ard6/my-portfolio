# My Portfolio

Portafolio personal para centralizar y mostrar proyectos a reclutadores, con gestión
de proyectos y tecnologías desde una base de datos.

## 🧭 Propósito

Este proyecto busca concentrar en un solo lugar todos los proyectos realizados,
permitiendo a cualquier visitante verlos sin registro previo y a uno o más
administradores gestionarlos (crear, editar, archivar y restaurar).

## 🚀 Cómo ejecutar el proyecto (local)

1. Configura tu servidor local (Laragon, XAMPP, etc.) apuntando a `public/`.
2. Crea la base de datos y las tablas necesarias (ver esquema abajo).
3. Crea un archivo `.emp` (o `.env`) a partir de `.emp.example` y completa las credenciales de DB.
4. Inicia el servidor y entra a `http://localhost/`.

## 🔐 Configuración de entorno

El proyecto carga variables primero desde `.env` y, si no existe, desde `.emp`.

Variables usadas:

- `DB_HOST`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `DB_CHARSET` (opcional, default `utf8mb4`)

> Nunca subas `.env` o `.emp` al repositorio. Ya están excluidos en `.gitignore`.

## ⚙️ Tecnologías y arquitectura

- **PHP** (MVC simple con `controllers`, `models`, `views`)
- **MySQL** (persistencia de proyectos, tecnologías y usuarios admin)
- **HTML/CSS** (interfaz y estilos)

Estructura principal:

- `app/controllers`: controladores de la aplicación
- `app/models`: acceso a base de datos
- `core`: router, vista y utilidades
- `public`: punto de entrada (`index.php`)

## ✅ Funcionalidades implementadas

### Público (visitantes)

- Ver listado de proyectos
- Ver detalle de un proyecto
- Filtrar proyectos por estado (pendiente, activo, completado)

### Administración (solo admin)

- Login de administrador contra tabla `admin_users`
- Crear proyectos
- Editar proyectos
- Actualizar estados
- Archivar y restaurar proyectos
- Ver listado de archivados
- CRUD de administradores (`/admins`)

## 🗃️ Esquema de base de datos (mínimo)

Las tablas principales que se usan en el proyecto son:

- `projects`
- `technologies`
- `project_technology` (tabla pivote)
- `admin_users`

### SQL base recomendado

```sql
CREATE TABLE admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

Insertar un admin inicial (ejemplo):

```sql
INSERT INTO admin_users (username, email, password_hash, is_active)
VALUES ('admin', 'admin@tu-dominio.com', '$2y$12$REEMPLAZAR_CON_HASH_REAL', 1);
```

Generar hash seguro en local:

```bash
php -r "echo password_hash('TuPasswordSegura', PASSWORD_DEFAULT), PHP_EOL;"
```


## 🧩 ¿Qué pasó con el error fatal que viste?

El error que te apareció:

- `Table 'portfolio.admin_users' doesn't exist`

significa que el código ya está intentando validar el login en la tabla `admin_users`,
pero esa tabla aún no existe en tu base de datos local.

Piensa así:

1. Antes: el usuario admin estaba "quemado" en código.
2. Ahora: el usuario admin se busca en DB (más seguro y escalable).
3. Si la tabla no existe todavía, MySQL no sabe dónde buscar y explota.

Para evitar pantalla blanca/fatal, ahora el sistema valida si la tabla existe. Si no existe,
el login falla de forma controlada con mensaje de credenciales/configuración en vez de romper todo.

## 🧱 SQL completo (copiar y pegar) para dejar `admin_users` lista

> Ejecuta esto en tu base `portfolio` (phpMyAdmin, MySQL Workbench o consola).

```sql
USE portfolio;

CREATE TABLE IF NOT EXISTS admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Crear tu primer admin (paso a paso)

1) Genera el hash en PHP (NO guardes contraseña en texto plano):

```bash
php -r "echo password_hash('admin123', PASSWORD_DEFAULT), PHP_EOL;"
```

2) Copia el hash resultante y ejecuta:

```sql
INSERT INTO admin_users (username, email, password_hash, is_active)
VALUES ('admin', 'admin@tu-dominio.com', 'PEGA_AQUI_EL_HASH', 1);
```

3) Verifica:

```sql
SELECT id, username, email, is_active, created_at FROM admin_users;
```

Si ya tenías la tabla creada sin correo, **no uses** `NOT NULL UNIQUE` en un solo paso (puede fallar con error 1062 por valores duplicados vacíos). Haz esta migración segura en 4 pasos:

```sql
-- 1) Agregar columna permitiendo NULL temporalmente
ALTER TABLE admin_users
  ADD COLUMN email VARCHAR(190) NULL AFTER username;

-- 2) Rellenar emails únicos para filas existentes (ejemplo temporal)
UPDATE admin_users
SET email = CONCAT('admin', id, '@change-me.local')
WHERE email IS NULL OR email = '';

-- 3) Verificar que no haya duplicados antes de crear índice UNIQUE
SELECT email, COUNT(*) AS total
FROM admin_users
GROUP BY email
HAVING COUNT(*) > 1;

-- 4) Ya limpio: forzar NOT NULL + UNIQUE
ALTER TABLE admin_users
  MODIFY COLUMN email VARCHAR(190) NOT NULL,
  ADD UNIQUE KEY uq_admin_users_email (email);
```

> Después de eso, edita cada admin y reemplaza los correos temporales (`@change-me.local`) por correos reales.


Si en tu gestor visual ves datos "corridos" (por ejemplo el email bajo columna `password_hash`), revisa la estructura real:

```sql
SHOW CREATE TABLE admin_users;
```

Debe incluir columnas en este orden recomendado:
`id, username, email, password_hash, is_active, created_at`.

Con eso ya debe funcionar `/auth/login` con:

- Usuario: `admin`
- Contraseña: la que usaste al generar el hash (por ejemplo `admin123`).


## 🔑 Recuperación de contraseña por token (base de datos)

Para habilitar recuperación por token, crea esta tabla:

```sql
CREATE TABLE IF NOT EXISTS admin_password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  admin_user_id INT NOT NULL,
  token_hash CHAR(64) NOT NULL,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_admin_password_resets_token (token_hash),
  INDEX idx_admin_password_resets_admin (admin_user_id),
  CONSTRAINT fk_admin_password_resets_admin
    FOREIGN KEY (admin_user_id) REFERENCES admin_users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

Rutas incluidas:
- `/auth/forgot`
- `/auth/reset/{token}`

> En local, el enlace de recuperación se escribe en `storage/password_reset_links.log` (y también en `error_log`).

### ¿Cómo probar recuperación por token en local? (paso a paso)

1. Abre `/auth/forgot` e ingresa un correo admin existente.
2. El sistema genera un token y escribe el enlace en el log del servidor (no envía correo real todavía).
3. Busca una línea similar a:
   `Token reset para correo@dominio.com: http://localhost/auth/reset/TOKEN...`
4. Copia ese enlace y pégalo en el navegador.
5. Define nueva contraseña y confirma.

> El token vence en **30 minutos**. Si falla de inmediato, revisa hora/zona horaria del servidor MySQL y PHP.

Ejemplos de dónde revisar logs:
- **Primera opción recomendada**: archivo del proyecto `storage/password_reset_links.log`.
- **También disponible**: `error.log` de Apache/PHP si tu servidor lo permite.

> Si el correo no existe en `admin_users`, ahora el sistema te lo informa antes de generar token.
> Si quieres correo real, el siguiente paso es configurar SMTP y reemplazar el `error_log(...)`/archivo local por envío de email.



## 🔗 Enlaces de proyectos y estados (requisito para producción)

Para que el botón **Abrir proyecto** funcione correctamente por estado (`pending`, `active`, `completed`), la tabla `projects` debe tener una columna de URL.

Migración recomendada:

```sql
ALTER TABLE projects
  ADD COLUMN project_url VARCHAR(255) NULL AFTER description;
```

También dejé este SQL listo para ejecutar en local:

- `database/migrations/20260216_add_project_url.sql`

Si en tu BD ya existe una columna legacy (`project_link` o `url`), el sistema la detecta automáticamente. Aun así, lo ideal es estandarizar en `project_url`.

> Nota: al guardar/editar un proyecto con URL, el sistema intenta crear `project_url` automáticamente si tu usuario MySQL tiene permisos de `ALTER TABLE`. En producción, igual se recomienda ejecutar la migración de forma explícita antes del despliegue.


Comportamiento del botón **Abrir proyecto**:

- `pending`: muestra vista informativa (**proyecto aún no iniciado**).
- `active`: redirige al enlace si existe y es válido; si no existe, muestra aviso.
- `completed`: redirige al enlace final si existe y es válido; si no existe, muestra aviso.

Validación sugerida antes de subir a producción:

```sql
SELECT id, name, status, project_url
FROM projects
ORDER BY id DESC;
```



## 🧪 Migración para activar/inactivar tecnologías

Para habilitar el nuevo CRUD de tecnologías con estado (`activa` / `inactiva`), ejecuta:

```sql
ALTER TABLE technologies
  ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER name;
```

Archivo listo para ejecutar:

- `database/migrations/20260216_add_technology_is_active.sql`

> Si no aplicas esta migración, el CRUD seguirá funcionando en modo básico (solo nombre, sin estado).



## ✍️ Edición de contenido público (Sobre mí y Contacto)

Cuando un administrador inicia sesión, en la vista **/about** aparece un panel para editar el contenido público de:

- Sobre mí
- Contacto

Ese contenido se guarda en `storage/site_content.json` (sin requerir cambios de base de datos).

Si no existe el archivo, se usan valores por defecto.

## 🧾 Historial de cambios de administradores (opcional recomendado)

Para registrar quién modificó a qué admin y cuándo, crea esta tabla:

```sql
CREATE TABLE IF NOT EXISTS admin_audit_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  action VARCHAR(80) NOT NULL,
  performed_by VARCHAR(80) NOT NULL,
  target_admin_id INT NULL,
  details VARCHAR(255) NOT NULL DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_admin_audit_target (target_admin_id),
  CONSTRAINT fk_admin_audit_target
    FOREIGN KEY (target_admin_id) REFERENCES admin_users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

> Si no creas esta tabla, el sistema igual funciona; solo no mostrará historial.

### Mejora opcional de auditoría (recomendada)

Para que el historial siga mostrando correctamente el **nombre actual** del admin que realizó cambios, guarda también su ID:

```sql
ALTER TABLE admin_audit_logs
  ADD COLUMN performed_by_admin_id INT NULL AFTER performed_by,
  ADD INDEX idx_admin_audit_performer (performed_by_admin_id),
  ADD CONSTRAINT fk_admin_audit_performer
    FOREIGN KEY (performed_by_admin_id) REFERENCES admin_users(id)
    ON DELETE SET NULL;
```

> Si no aplicas este ALTER, el sistema sigue funcionando y usa el nombre guardado en texto (`performed_by`).

## 🧪 Rutas principales

### Públicas

- `/` (inicio)
- `/projects` (listado y filtros)
- `/projects/show/:id` (detalle)
- `/about`
- `/contact` (redirige a `/about#contacto`)

### Admin

- `/auth/login`
- `/auth/logout`
- `/auth/forgot`
- `/auth/reset/:token`
- `/projects/create`
- `/projects/edit/:id`
- `/projects/archived`
- `/admins`
- `/admins/create`
- `/admins/edit/:id`
- `/technologies`
- `/technologies/create`
- `/technologies/edit/:id`
- `/about/save` (editar Sobre mí/Contacto desde panel admin)

## 🛡️ Recomendaciones de seguridad (prioridad)

1. Limitar intentos de login (rate limiting o bloqueo temporal). ✅ Implementado en sesión por usuario+IP.
2. Registrar auditoría básica (fecha IP/usuario de login correcto e incorrecto).
3. Forzar HTTPS en despliegue y cookies de sesión `Secure`, `HttpOnly`, `SameSite`.
4. Rotar contraseñas y evitar usuarios admin compartidos.
5. Definir política de backups y restauración de la DB.
6. Evitar mensajes de error detallados en producción (`display_errors=Off`).

## 🚧 Pendientes / Próximos pasos sugeridos

- Separar panel de administración en ruta `/admin`.
- Conectar envío real de correos SMTP para recuperación por token (actualmente se registra en log para entorno local).
- Añadir historial de cambios para proyectos (similar al historial de admins).
- Implementar tests mínimos para autenticación y modelo de proyectos.
- Agregar migraciones SQL versionadas.
- Configurar CI para validación automática (lint + smoke tests).

## 🧭 Flujo recomendado de ramas

- `main`: versión estable (producción)
- `develop`: integración de cambios
- `feature/*`: ramas temporales para nuevas funciones

---

> Nota: Si en el futuro quieres un README en inglés, se puede crear un `README.en.md`
> y mantener este como principal en español.

# My Portfolio

Portafolio personal para centralizar y mostrar proyectos a reclutadores, con gestión
de proyectos y tecnologías desde una base de datos.

## 🧭 Propósito

Este proyecto busca concentrar en un solo lugar todos los proyectos realizados,
permitiendo a cualquier visitante verlos y a un administrador gestionarlos
(crear, editar, archivar y restaurar).

## 🚀 Cómo ejecutar el proyecto (local)

1. Configura tu servidor local (Laragon, XAMPP, etc.) apuntando a `public/`.
2. Crea la base de datos y las tablas necesarias (ver esquema abajo).
3. Ajusta las credenciales en `core/Database.php`.
4. Inicia el servidor y entra a `http://localhost/`.

## ⚙️ Tecnologías y arquitectura

- **PHP** (MVC simple con `controllers`, `models`, `views`)
- **MySQL** (persistencia de proyectos y tecnologías)
- **HTML/CSS** (interfaz y estilos)

Estructura principal:

- `app/controllers`: controladores de la aplicación
- `app/models`: acceso a base de datos
- `app/views`: vistas renderizadas
- `core`: router, vista y utilidades
- `public`: punto de entrada (`index.php`)

## ✅ Funcionalidades implementadas

### Público (visitantes)

- Ver listado de proyectos
- Ver detalle de un proyecto
- Filtrar proyectos por estado (pendiente, activo, completado)

### Administración (solo admin)

- Crear proyectos
- Editar proyectos
- Actualizar estados
- Archivar y restaurar proyectos
- Ver listado de archivados

## 🗃️ Esquema de base de datos (mínimo)

Las tablas principales que se usan en el proyecto son:

- `projects`
- `technologies`
- `project_technology` (tabla pivote)

Campos sugeridos:

### projects
- `id` (INT, PK)
- `name` (VARCHAR)
- `description` (TEXT)
- `status` (VARCHAR: pending | active | completed | archived)
- `created_at` (TIMESTAMP)

### technologies
- `id` (INT, PK)
- `name` (VARCHAR)

### project_technology
- `project_id` (FK a projects.id)
- `technology_id` (FK a technologies.id)

## 🔐 Acceso de administrador

Se agregó un login básico para proteger la edición de proyectos.

**Credenciales por defecto:**

- Usuario: `admin`
- Contraseña: `admin123`

Si quieres cambiarlo, define estas variables de entorno:

- `PORTFOLIO_ADMIN_USER`
- `PORTFOLIO_ADMIN_HASH` (usa `password_hash()` en PHP para generar el hash)

Ejemplo para generar el hash:

```bash
php -r "echo password_hash('TuPassword', PASSWORD_DEFAULT);"

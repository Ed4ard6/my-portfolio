# My Portfolio

Portafolio personal para centralizar y mostrar proyectos a reclutadores, con gestión
de proyectos y tecnologías desde una base de datos.

## 🧭 Propósito

Este proyecto busca concentrar en un solo lugar todos los proyectos realizados,
permitiendo a cualquier visitante verlos y a un administrador gestionarlos
(crear, editar, archivar y restaurar).

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

## 🔐 Acceso de administrador

Se agregó un login básico para proteger la edición de proyectos.

**Credenciales por defecto:**
# my-portfolio
Personal portfolio website showcasing my projects, skills, and experience as a developer.

## Acceso de administrador

Para proteger la edición de proyectos, se agregó un login básico.

Credenciales por defecto:

- Usuario: `admin`
- Contraseña: `admin123`

Si quieres cambiarlo, define estas variables de entorno:

- `PORTFOLIO_ADMIN_USER`
- `PORTFOLIO_ADMIN_HASH` (usa `password_hash()` en PHP para generar el hash)

## 🧪 Rutas principales

### Públicas

- `/` (inicio)
- `/projects` (listado y filtros)
- `/projects/show/:id` (detalle)
- `/about`
- `/contact`

### Admin

- `/auth/login`
- `/auth/logout`
- `/projects/create`
- `/projects/edit/:id`
- `/projects/archived`

## ⚠️ Solución de errores comunes
## Solución de errores comunes

### Error: Cannot redeclare ProjectModel::filterByStatus()

Este error aparece cuando hay **dos métodos `filterByStatus()` dentro de la clase**
`ProjectModel`. Debes dejar **solo uno**. El método correcto es el que usa la tabla
`project_technology` (singular) y el mismo conjunto de columnas que `all()`.

## 🚧 Pendientes / Próximos pasos

- Añadir campo de URL del proyecto (para enlazar repos o demos)
- Separar panel de administración en una ruta `/admin`
- Agregar protección CSRF en formularios
- Validaciones más robustas en edición

---

> Nota: Si en el futuro quieres un README en inglés, se puede crear un `README.en.md`
> y mantener este como principal en español.
`project_technology` (singular) y el mismo conjunto de columnas que `all()`.【F:app/models/ProjectModel.php†L5-L232】

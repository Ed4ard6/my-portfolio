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

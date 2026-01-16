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

## Solución de errores comunes

### Error: Cannot redeclare ProjectModel::filterByStatus()

Este error aparece cuando hay **dos métodos `filterByStatus()` dentro de la clase**
`ProjectModel`. Debes dejar **solo uno**. El método correcto es el que usa la tabla
`project_technology` (singular) y el mismo conjunto de columnas que `all()`.【F:app/models/ProjectModel.php†L5-L232】

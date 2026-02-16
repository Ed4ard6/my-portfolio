-- Agrega columna estándar para enlace público de cada proyecto.
ALTER TABLE projects
  ADD COLUMN project_url VARCHAR(255) NULL AFTER description;

-- Verificación rápida.
SELECT id, name, status, project_url
FROM projects
ORDER BY id DESC;

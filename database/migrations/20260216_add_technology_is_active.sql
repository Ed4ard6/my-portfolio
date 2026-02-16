-- Agrega bandera de estado para gestionar tecnologías activas/inactivas.
ALTER TABLE technologies
  ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER name;

-- Verificación rápida.
SELECT id, name, is_active, created_at
FROM technologies
ORDER BY name ASC;

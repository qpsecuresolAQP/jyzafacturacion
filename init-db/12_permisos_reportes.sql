

INSERT INTO permisos (controller, action, descripcion, created, modified)
VALUES ('Reportes', 'index', 'Ver reportes de comprobantes', NOW(), NOW());

-- Asigna ese permiso al rol 1
-- NOTA: el 240 debe coincidir con el ID real generado por el INSERT de arriba.
-- Si la tabla tiene AUTO_INCREMENT diferente, reemplaza 240 con LAST_INSERT_ID()
INSERT INTO roles_permisos (rol_id, permiso_id, created, modified)
VALUES (1, 240, NOW(), NOW());
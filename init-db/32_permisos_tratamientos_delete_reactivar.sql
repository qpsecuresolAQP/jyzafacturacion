INSERT IGNORE INTO permisos (controller, action, descripcion, created, modified)
VALUES
('Tratamientos', 'delete', 'Desactivar tratamiento', NOW(), NOW()),
('Tratamientos', 'reactivar', 'Reactivar tratamiento', NOW(), NOW());

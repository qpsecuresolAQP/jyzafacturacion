-- Calendario Especialistas: vista de citas por especialista (columnas = doctores, filas = horas)
-- No agrega tablas nuevas, solo el permiso de acceso al módulo (mismo patrón que `citaDiaria`)

INSERT IGNORE INTO `permisos` (`controller`, `action`, `descripcion`, `created`, `modified`) VALUES
('Citas', 'calendarioEspecialistas', 'Ver calendario de citas por especialista', NOW(), NOW());

-- Conceder el permiso a los mismos roles que ya tienen `citaDiaria` (Admin, Doctor, Recepcion)
INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`, `created`, `modified`)
SELECT r.id, p.id, NOW(), NOW()
FROM `roles` r
CROSS JOIN `permisos` p
WHERE p.controller = 'Citas' AND p.action = 'calendarioEspecialistas'
  AND r.nombre IN ('Admin', 'Doctor', 'Recepcion');

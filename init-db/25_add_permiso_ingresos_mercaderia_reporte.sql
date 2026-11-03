-- Permiso para el reporte de compras (IngresosMercaderia::reporte): ingresos de
-- mercadería por rango de fechas y por proveedor. Asignado a Admin.
INSERT INTO `permisos` (`controller`, `action`, `descripcion`, `created`, `modified`)
SELECT 'IngresosMercaderia', 'reporte', 'Reporte de compras por fecha y proveedor', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `permisos` WHERE `controller` = 'IngresosMercaderia' AND `action` = 'reporte'
);

INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`)
SELECT 1, p.id
FROM `permisos` p
WHERE p.`controller` = 'IngresosMercaderia' AND p.`action` = 'reporte'
  AND NOT EXISTS (
      SELECT 1 FROM `roles_permisos` rp
      WHERE rp.`rol_id` = 1 AND rp.`permiso_id` = p.id
  );

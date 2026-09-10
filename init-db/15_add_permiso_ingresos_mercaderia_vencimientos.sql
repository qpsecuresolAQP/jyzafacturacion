-- Permiso para el reporte de vencimientos de mercadería
-- (IngresosMercaderia::vencimientos), que lista los lotes ingresados por
-- fecha de vencimiento: vencidos, por vencer y vigentes. Asignado a Admin.
INSERT INTO `permisos` (`controller`, `action`, `descripcion`, `created`, `modified`)
SELECT 'IngresosMercaderia', 'vencimientos', 'Reporte de vencimientos de mercadería', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `permisos` WHERE `controller` = 'IngresosMercaderia' AND `action` = 'vencimientos'
);

INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`)
SELECT 1, p.id
FROM `permisos` p
WHERE p.`controller` = 'IngresosMercaderia' AND p.`action` = 'vencimientos'
  AND NOT EXISTS (
      SELECT 1 FROM `roles_permisos` rp
      WHERE rp.`rol_id` = 1 AND rp.`permiso_id` = p.id
  );

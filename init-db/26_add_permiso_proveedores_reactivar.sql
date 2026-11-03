-- Permiso para Proveedores::reactivar(). La eliminación de proveedores pasa a ser
-- lógica (activo = 0) para conservar su historial de compras. Asignado a Admin.
INSERT INTO `permisos` (`controller`, `action`, `descripcion`, `created`, `modified`)
SELECT 'Proveedores', 'reactivar', 'Reactivar proveedor', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `permisos` WHERE `controller` = 'Proveedores' AND `action` = 'reactivar'
);

INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`)
SELECT 1, p.id
FROM `permisos` p
WHERE p.`controller` = 'Proveedores' AND p.`action` = 'reactivar'
  AND NOT EXISTS (
      SELECT 1 FROM `roles_permisos` rp
      WHERE rp.`rol_id` = 1 AND rp.`permiso_id` = p.id
  );

-- Permiso para la nueva acción Productos::reactivar(), que permite
-- reactivar un producto individualmente desde el apartado de "productos
-- inactivos" en la vista de su categoría (además de la reactivación en
-- cascada al reactivar toda la categoría). Asignado al rol Admin.
INSERT INTO `permisos` (`controller`, `action`, `descripcion`, `created`, `modified`)
SELECT 'Productos', 'reactivar', 'Reactivar producto', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `permisos` WHERE `controller` = 'Productos' AND `action` = 'reactivar'
);

INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`)
SELECT 1, p.id
FROM `permisos` p
WHERE p.`controller` = 'Productos' AND p.`action` = 'reactivar'
  AND NOT EXISTS (
      SELECT 1 FROM `roles_permisos` rp
      WHERE rp.`rol_id` = 1 AND rp.`permiso_id` = p.id
  );

-- Agrega el permiso Productos/delete, faltante desde que se creó el módulo
-- de productos. El controller ya tiene la acción delete() (desactivación
-- lógica: estado = 0), pero sin este permiso el botón quedaba oculto y el
-- índice de productos apuntaba erróneamente a una acción "toggleStatus" que
-- nunca existió. Se asigna al rol Admin.
INSERT INTO `permisos` (`controller`, `action`, `descripcion`, `created`, `modified`)
SELECT 'Productos', 'delete', 'Desactivar producto', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `permisos` WHERE `controller` = 'Productos' AND `action` = 'delete'
);

INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`)
SELECT 1, p.id
FROM `permisos` p
WHERE p.`controller` = 'Productos' AND p.`action` = 'delete'
  AND NOT EXISTS (
      SELECT 1 FROM `roles_permisos` rp
      WHERE rp.`rol_id` = 1 AND rp.`permiso_id` = p.id
  );

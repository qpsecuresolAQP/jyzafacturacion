-- Agrega el permiso CategoriasProductos/reactivar. Antes, el botón "Activar"
-- de una categoría desactivada apuntaba a la misma acción delete(), que
-- siempre fuerza estado = 0 sin importar el estado actual: nunca reactivaba
-- nada. Ahora existe una acción reactivar() real (igual que en
-- CategoriasExamenes) y este permiso, asignado al rol Admin.
INSERT INTO `permisos` (`controller`, `action`, `descripcion`, `created`, `modified`)
SELECT 'CategoriasProductos', 'reactivar', 'Reactivar categoría de producto', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `permisos` WHERE `controller` = 'CategoriasProductos' AND `action` = 'reactivar'
);

INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`)
SELECT 1, p.id
FROM `permisos` p
WHERE p.`controller` = 'CategoriasProductos' AND p.`action` = 'reactivar'
  AND NOT EXISTS (
      SELECT 1 FROM `roles_permisos` rp
      WHERE rp.`rol_id` = 1 AND rp.`permiso_id` = p.id
  );

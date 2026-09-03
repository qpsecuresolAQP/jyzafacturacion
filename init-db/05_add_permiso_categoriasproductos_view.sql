-- Agrega el permiso CategoriasProductos/view, faltante desde que se creó el
-- módulo de categorías de productos, y lo asigna al rol Admin. Sin este
-- permiso el botón "Ver" del listado de categorías queda oculto para todos.
INSERT INTO `permisos` (`controller`, `action`, `descripcion`, `created`, `modified`)
SELECT 'CategoriasProductos', 'view', 'Ver detalle de categoría de producto', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `permisos` WHERE `controller` = 'CategoriasProductos' AND `action` = 'view'
);

INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`)
SELECT 1, p.id
FROM `permisos` p
WHERE p.`controller` = 'CategoriasProductos' AND p.`action` = 'view'
  AND NOT EXISTS (
      SELECT 1 FROM `roles_permisos` rp
      WHERE rp.`rol_id` = 1 AND rp.`permiso_id` = p.id
  );

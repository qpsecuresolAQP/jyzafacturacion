-- Historial de ingresos/egresos manuales de stock por producto (compras,
-- mermas, ajustes de inventario, uso interno, etc.), para no perder
-- trazabilidad de por qué cambió el stock al editarlo directamente.
CREATE TABLE IF NOT EXISTS `producto_movimientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `tipo` enum('ingreso','egreso') NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `stock_anterior` decimal(10,2) NOT NULL,
  `stock_nuevo` decimal(10,2) NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_producto_movimientos_producto` (`producto_id`),
  KEY `fk_producto_movimientos_usuario` (`usuario_id`),
  CONSTRAINT `fk_producto_movimientos_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `fk_producto_movimientos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Permisos del nuevo módulo, asignados al rol Admin.
INSERT INTO `permisos` (`controller`, `action`, `descripcion`, `created`, `modified`)
SELECT 'ProductoMovimientos', 'add', 'Registrar ingreso/egreso de stock', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `permisos` WHERE `controller` = 'ProductoMovimientos' AND `action` = 'add'
);

INSERT INTO `permisos` (`controller`, `action`, `descripcion`, `created`, `modified`)
SELECT 'ProductoMovimientos', 'historial', 'Ver historial de movimientos de stock', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `permisos` WHERE `controller` = 'ProductoMovimientos' AND `action` = 'historial'
);

INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`)
SELECT 1, p.id
FROM `permisos` p
WHERE p.`controller` = 'ProductoMovimientos' AND p.`action` IN ('add', 'historial')
  AND NOT EXISTS (
      SELECT 1 FROM `roles_permisos` rp
      WHERE rp.`rol_id` = 1 AND rp.`permiso_id` = p.id
  );

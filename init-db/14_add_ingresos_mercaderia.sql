-- Módulo de Ingreso de Mercadería (ingreso de stock por documento de compra).
-- Cada ingreso es una compra a un proveedor con su documento (factura/boleta/
-- guía), una o varias líneas de producto, y al registrarse suma el stock de
-- cada producto y deja un producto_movimientos de tipo 'ingreso' por línea.

-- Datos de proveedor que el ingreso necesita mostrar/guardar y que la tabla
-- proveedores no tenía.
ALTER TABLE `proveedores`
  ADD COLUMN `ruc` varchar(11) DEFAULT NULL AFTER `nombre`,
  ADD COLUMN `direccion` varchar(255) DEFAULT NULL AFTER `ruc`;

-- Cabecera del ingreso.
CREATE TABLE IF NOT EXISTS `ingresos_mercaderia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `almacen` varchar(100) NOT NULL DEFAULT 'Almacen Principal',
  `tipo_doc` enum('FACTURA','BOLETA','GUIA','OTRO') NOT NULL DEFAULT 'FACTURA',
  `serie` varchar(20) DEFAULT NULL,
  `numero` varchar(30) DEFAULT NULL,
  `moneda` enum('SOLES','DOLARES') NOT NULL DEFAULT 'SOLES',
  `fecha_ingreso` datetime NOT NULL,
  `fecha_factura` date DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `igv` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `observacion` text DEFAULT NULL,
  `estado` enum('REGISTRADO','ANULADO') NOT NULL DEFAULT 'REGISTRADO',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ingmerc_proveedor` (`proveedor_id`),
  KEY `fk_ingmerc_usuario` (`usuario_id`),
  CONSTRAINT `fk_ingmerc_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `fk_ingmerc_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Detalle: una línea por producto ingresado.
CREATE TABLE IF NOT EXISTS `ingresos_mercaderia_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ingreso_mercaderia_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(12,2) NOT NULL,
  `precio_unitario` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `igv` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `lote` varchar(50) DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ingmercdet_ingreso` (`ingreso_mercaderia_id`),
  KEY `fk_ingmercdet_producto` (`producto_id`),
  CONSTRAINT `fk_ingmercdet_ingreso` FOREIGN KEY (`ingreso_mercaderia_id`) REFERENCES `ingresos_mercaderia` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ingmercdet_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Enlace opcional del movimiento de stock al ingreso que lo generó, para
-- poder revertir la cascada si se anula el ingreso.
ALTER TABLE `producto_movimientos`
  ADD COLUMN `ingreso_mercaderia_id` int(11) DEFAULT NULL AFTER `usuario_id`,
  ADD KEY `fk_prodmov_ingreso` (`ingreso_mercaderia_id`),
  ADD CONSTRAINT `fk_prodmov_ingreso` FOREIGN KEY (`ingreso_mercaderia_id`) REFERENCES `ingresos_mercaderia` (`id`) ON DELETE SET NULL;

-- Permisos del nuevo módulo, asignados al rol Admin.
INSERT INTO `permisos` (`controller`, `action`, `descripcion`, `created`, `modified`)
SELECT v.controller, v.action, v.descripcion, NOW(), NOW()
FROM (
  SELECT 'IngresosMercaderia' AS controller, 'index'  AS action, 'Listar ingresos de mercadería'  AS descripcion
  UNION ALL SELECT 'IngresosMercaderia', 'view',   'Ver detalle de ingreso de mercadería'
  UNION ALL SELECT 'IngresosMercaderia', 'add',    'Registrar ingreso de mercadería'
  UNION ALL SELECT 'IngresosMercaderia', 'anular', 'Anular ingreso de mercadería'
) v
WHERE NOT EXISTS (
  SELECT 1 FROM `permisos` p WHERE p.`controller` = v.controller AND p.`action` = v.action
);

INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`)
SELECT 1, p.id
FROM `permisos` p
WHERE p.`controller` = 'IngresosMercaderia'
  AND NOT EXISTS (
      SELECT 1 FROM `roles_permisos` rp
      WHERE rp.`rol_id` = 1 AND rp.`permiso_id` = p.id
  );

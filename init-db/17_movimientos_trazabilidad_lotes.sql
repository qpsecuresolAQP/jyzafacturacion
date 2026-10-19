-- Trazabilidad de movimientos de stock por lote y control de vencimientos.
--  * Cada movimiento guarda de qué lote(s) salió o entró.
--  * Las ventas y sus anulaciones también quedan en el historial (invoice_id).
--  * Motivo codificado (vencido, merma, rotura...) y costo al momento del
--    movimiento, para valorizar pérdidas aunque el costo cambie después.
--  * Productos que exigen lote y fecha de vencimiento al ingresar.
--  * Cambios al número/vencimiento de un lote quedan auditados.

ALTER TABLE `productos`
  ADD COLUMN `requiere_vencimiento` tinyint(1) NOT NULL DEFAULT 1 AFTER `estado`;

ALTER TABLE `producto_movimientos`
  ADD COLUMN `motivo_codigo` varchar(30) DEFAULT NULL AFTER `tipo`,
  ADD COLUMN `invoice_id` int(11) DEFAULT NULL AFTER `ingreso_mercaderia_id`,
  ADD COLUMN `costo_unitario` decimal(12,4) DEFAULT NULL AFTER `stock_nuevo`,
  ADD KEY `idx_pm_motivo_fecha` (`motivo_codigo`, `created`),
  ADD KEY `fk_pm_invoice` (`invoice_id`),
  ADD CONSTRAINT `fk_pm_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS `producto_movimiento_lotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_movimiento_id` int(11) NOT NULL,
  `producto_lote_id` int(11) NOT NULL,
  `cantidad` decimal(12,2) NOT NULL COMMENT 'Unidades que se movieron en ese lote (positivo; el sentido lo da el tipo del movimiento)',
  PRIMARY KEY (`id`),
  KEY `fk_pml_movimiento` (`producto_movimiento_id`),
  KEY `fk_pml_lote` (`producto_lote_id`),
  CONSTRAINT `fk_pml_movimiento` FOREIGN KEY (`producto_movimiento_id`) REFERENCES `producto_movimientos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pml_lote` FOREIGN KEY (`producto_lote_id`) REFERENCES `producto_lotes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `producto_lote_cambios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_lote_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `campo` varchar(30) NOT NULL,
  `valor_anterior` varchar(100) DEFAULT NULL,
  `valor_nuevo` varchar(100) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_plc_lote` (`producto_lote_id`),
  KEY `fk_plc_usuario` (`usuario_id`),
  CONSTRAINT `fk_plc_lote` FOREIGN KEY (`producto_lote_id`) REFERENCES `producto_lotes` (`id`),
  CONSTRAINT `fk_plc_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Cada línea de una compra apunta al lote que alimentó. Con lotes repetidos
-- (misma compra del mismo lote en dos entregas) varias líneas comparten lote.
ALTER TABLE `ingresos_mercaderia_detalle`
  ADD COLUMN `producto_lote_id` int(11) DEFAULT NULL AFTER `fecha_vencimiento`,
  ADD KEY `fk_imd_lote` (`producto_lote_id`),
  ADD CONSTRAINT `fk_imd_lote` FOREIGN KEY (`producto_lote_id`) REFERENCES `producto_lotes` (`id`) ON DELETE SET NULL;

UPDATE `ingresos_mercaderia_detalle` d
JOIN `producto_lotes` l ON l.`ingreso_mercaderia_detalle_id` = d.`id`
SET d.`producto_lote_id` = l.`id`;

-- Movimientos anteriores: motivo según su origen y costo actual como referencia.
UPDATE `producto_movimientos`
SET `motivo_codigo` = IF(`tipo` = 'ingreso', 'COMPRA', 'ANULACION_COMPRA')
WHERE `motivo_codigo` IS NULL AND `ingreso_mercaderia_id` IS NOT NULL;

UPDATE `producto_movimientos` m
JOIN `productos` p ON p.`id` = m.`producto_id`
SET m.`costo_unitario` = p.`precio_compra`
WHERE m.`costo_unitario` IS NULL;

-- Movimientos de compra anteriores: se enlazan a su lote.
INSERT INTO `producto_movimiento_lotes` (`producto_movimiento_id`, `producto_lote_id`, `cantidad`)
SELECT m.`id`, MIN(d.`producto_lote_id`), m.`cantidad`
FROM `producto_movimientos` m
JOIN `ingresos_mercaderia_detalle` d
  ON d.`ingreso_mercaderia_id` = m.`ingreso_mercaderia_id`
 AND d.`producto_id` = m.`producto_id`
 AND d.`producto_lote_id` IS NOT NULL
 AND ABS(d.`cantidad` - m.`cantidad`) < 0.005
GROUP BY m.`id`, m.`cantidad`;

-- Permisos de las pantallas nuevas (Admin).
INSERT INTO `permisos` (`controller`, `action`, `descripcion`, `created`, `modified`)
SELECT v.controller, v.action, v.descripcion, NOW(), NOW()
FROM (
  SELECT 'ProductoMovimientos' AS controller, 'editarLote' AS action, 'Corregir número y vencimiento de un lote' AS descripcion
  UNION ALL SELECT 'ProductoMovimientos', 'perdidas', 'Reporte de pérdidas y bajas de stock'
) v
WHERE NOT EXISTS (
  SELECT 1 FROM `permisos` p WHERE p.`controller` = v.controller AND p.`action` = v.action
);

INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`)
SELECT 1, p.`id`
FROM `permisos` p
WHERE p.`controller` = 'ProductoMovimientos' AND p.`action` IN ('editarLote', 'perdidas')
  AND NOT EXISTS (
      SELECT 1 FROM `roles_permisos` x WHERE x.`rol_id` = 1 AND x.`permiso_id` = p.`id`
  );

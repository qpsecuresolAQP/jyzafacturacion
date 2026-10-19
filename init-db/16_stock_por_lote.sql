-- Stock por lote con fecha de vencimiento (FEFO: primero vence, primero sale).
-- productos.stock se mantiene como total del producto = suma del stock de sus
-- lotes, porque muchas pantallas lo leen; toda entrada/salida pasa por los lotes.

CREATE TABLE IF NOT EXISTS `producto_lotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `lote` varchar(50) DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `cantidad_inicial` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock` decimal(12,2) NOT NULL DEFAULT 0.00,
  `origen` enum('INGRESO','MOVIMIENTO','DEVOLUCION','STOCK_ANTERIOR') NOT NULL,
  `ingreso_mercaderia_detalle_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_producto_lotes_detalle` (`ingreso_mercaderia_detalle_id`),
  KEY `idx_producto_lotes_fefo` (`producto_id`, `fecha_vencimiento`),
  CONSTRAINT `fk_producto_lotes_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `fk_producto_lotes_detalle` FOREIGN KEY (`ingreso_mercaderia_detalle_id`) REFERENCES `ingresos_mercaderia_detalle` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- De qué lote(s) salió cada producto vendido: al anular la venta, el stock
-- vuelve exactamente a esos lotes.
CREATE TABLE IF NOT EXISTS `invoice_item_lotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_item_id` int(11) NOT NULL,
  `producto_lote_id` int(11) NOT NULL,
  `cantidad` decimal(12,2) NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_invoice_item_lotes_item` (`invoice_item_id`),
  KEY `fk_invoice_item_lotes_lote` (`producto_lote_id`),
  CONSTRAINT `fk_invoice_item_lotes_item` FOREIGN KEY (`invoice_item_id`) REFERENCES `invoice_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_invoice_item_lotes_lote` FOREIGN KEY (`producto_lote_id`) REFERENCES `producto_lotes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Repartir el stock actual en lotes. Se asume que lo que queda en stock es
-- lo último que ingresó: se recorren los ingresos vigentes del más reciente
-- al más antiguo y cada lote se queda con lo que alcance del stock actual.
-- Los ingresos ya consumidos quedan como lotes con stock 0 (trazabilidad).
INSERT INTO `producto_lotes`
  (`producto_id`, `lote`, `fecha_vencimiento`, `cantidad_inicial`, `stock`, `origen`, `ingreso_mercaderia_detalle_id`, `created`, `modified`)
SELECT
  x.`producto_id`,
  x.`lote`,
  x.`fecha_vencimiento`,
  x.`cantidad`,
  LEAST(x.`cantidad`, GREATEST(GREATEST(p.`stock`, 0) - (x.`acumulado` - x.`cantidad`), 0)),
  'INGRESO',
  x.`id`,
  COALESCE(x.`created`, NOW()),
  NOW()
FROM (
  SELECT
    d.`id`, d.`producto_id`, d.`lote`, d.`fecha_vencimiento`, d.`cantidad`, d.`created`,
    SUM(d.`cantidad`) OVER (PARTITION BY d.`producto_id` ORDER BY d.`id` DESC) AS `acumulado`
  FROM `ingresos_mercaderia_detalle` d
  JOIN `ingresos_mercaderia` i ON i.`id` = d.`ingreso_mercaderia_id` AND i.`estado` = 'REGISTRADO'
) x
JOIN `productos` p ON p.`id` = x.`producto_id`;

-- Stock que no se explica por ingresos registrados (stock inicial cargado a
-- mano, ajustes...): un lote "stock anterior" sin lote ni vencimiento.
INSERT INTO `producto_lotes`
  (`producto_id`, `lote`, `fecha_vencimiento`, `cantidad_inicial`, `stock`, `origen`, `created`, `modified`)
SELECT
  p.`id`, NULL, NULL,
  p.`stock` - COALESCE(l.`en_lotes`, 0),
  p.`stock` - COALESCE(l.`en_lotes`, 0),
  'STOCK_ANTERIOR', NOW(), NOW()
FROM `productos` p
LEFT JOIN (
  SELECT `producto_id`, SUM(`stock`) AS `en_lotes` FROM `producto_lotes` GROUP BY `producto_id`
) l ON l.`producto_id` = p.`id`
WHERE p.`stock` - COALESCE(l.`en_lotes`, 0) > 0;

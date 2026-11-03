-- Las líneas de un ingreso de mercadería pueden ser productos (suman stock por lote)
-- o procedimientos/exámenes (solo se registra la compra; no tienen stock).
ALTER TABLE `ingresos_mercaderia_detalle`
  MODIFY `producto_id` int(11) DEFAULT NULL,
  ADD COLUMN `tipo_item` enum('producto','tratamiento','examen') NOT NULL DEFAULT 'producto' AFTER `ingreso_mercaderia_id`,
  ADD COLUMN `tratamiento_id` int(11) DEFAULT NULL AFTER `producto_id`,
  ADD COLUMN `examen_id` int(11) DEFAULT NULL AFTER `tratamiento_id`,
  ADD KEY `fk_imd_tratamiento` (`tratamiento_id`),
  ADD KEY `fk_imd_examen` (`examen_id`),
  ADD CONSTRAINT `fk_imd_tratamiento` FOREIGN KEY (`tratamiento_id`) REFERENCES `tratamientos` (`id`),
  ADD CONSTRAINT `fk_imd_examen` FOREIGN KEY (`examen_id`) REFERENCES `examenes` (`id`);

ALTER TABLE `examenes`
  ADD COLUMN `laboratorio_id` int(11) DEFAULT NULL AFTER `precio_convenio`,
  ADD KEY `laboratorio_id` (`laboratorio_id`),
  ADD CONSTRAINT `examenes_ibfk_laboratorio` FOREIGN KEY (`laboratorio_id`) REFERENCES `laboratorios` (`id`);

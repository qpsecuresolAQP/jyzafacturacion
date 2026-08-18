--
-- Tabla `paquetes_pagos`
--
CREATE TABLE IF NOT EXISTS `paquetes_pagos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `historia_id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `num_sesiones` int(11) NOT NULL,
  `precio_total` decimal(10,2) NOT NULL,
  `tipo_pago` enum('contado','partes') NOT NULL DEFAULT 'contado',
  `fecha_recordatorio` date NOT NULL,
  `estado` enum('pendiente','pagado_parcial','pagado_completo','cancelado') NOT NULL DEFAULT 'pendiente',
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) NOT NULL,
  `estado_paquete` varchar(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `historia_id` (`historia_id`),
  KEY `estado` (`estado`),
  KEY `fecha_recordatorio` (`fecha_recordatorio`),
  CONSTRAINT `paquetes_pagos_ibfk_1` FOREIGN KEY (`historia_id`) REFERENCES `historias_clinicas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `paquetes_pagos_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tabla `paquetes_pagos_cuotas`
--
CREATE TABLE IF NOT EXISTS `paquetes_pagos_cuotas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paquete_pago_id` int(11) NOT NULL,
  `titulo_referencial` varchar(255) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_recordatorio` date NOT NULL,
  `estado` enum('pendiente','pagado') NOT NULL DEFAULT 'pendiente',
  `fecha_pago` datetime DEFAULT NULL,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `estado_cuota` varchar(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`id`),
  KEY `paquete_pago_id` (`paquete_pago_id`),
  KEY `estado` (`estado`),
  KEY `fecha_recordatorio` (`fecha_recordatorio`),
  CONSTRAINT `paquetes_pagos_cuotas_ibfk_1` FOREIGN KEY (`paquete_pago_id`) REFERENCES `paquetes_pagos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

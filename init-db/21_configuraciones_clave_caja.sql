-- Ajustes generales del sistema en formato clave/valor.
-- Primer uso: 'caja_password_hash' guarda la clave de acceso a Caja (hash, nunca en texto plano).
CREATE TABLE IF NOT EXISTS `configuraciones` (
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

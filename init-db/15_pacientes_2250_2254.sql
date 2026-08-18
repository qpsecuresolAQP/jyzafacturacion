-- Pacientes nuevos del VPS (2026-06-26)
-- 2250 (ARIANA)  → duplicado de 2177, se elimina
-- 2251 (JOAQUIN) → duplicado de 2206, se elimina (su historia 1200 también)
-- 2254 (NICHE)   → duplicado de 2191, se elimina
-- 2252 (ADRIAN)  → nuevo, se re-inserta como 2213
-- 2253 (VICTORIA)→ nuevo, se re-inserta como 2214

SET FOREIGN_KEY_CHECKS = 0;

-- Eliminar historia 1200 (era de paciente 2251, duplicado de 2206)
DELETE FROM `historias_clinicas` WHERE `id` = 1200;

-- Eliminar pacientes duplicados y los reemplazados
DELETE FROM `pacientes` WHERE `id` IN (2250, 2251, 2252, 2253, 2254);

-- Insertar los realmente nuevos con IDs correctos
INSERT IGNORE INTO `pacientes` (`id`, `nombre`, `apellido`, `telefono_celular`, `estado`, `created`, `modified`) VALUES
(2213, 'ADRIAN',   'RENGIFO',   '957416671', 'A', '2026-06-26 14:18:26', '2026-06-26 14:18:26'),
(2214, 'VICTORIA', 'RODRIGUEZ', '964612966', 'A', '2026-06-26 14:21:02', '2026-06-26 14:21:02');

ALTER TABLE `pacientes` AUTO_INCREMENT = 2215;

-- Recrear la historia de JOAQUIN (paciente 2206) que quedó sin historia
-- tras borrar la historia 1200 (era del duplicado 2251 en VPS)
INSERT IGNORE INTO `historias_clinicas`
  (`paciente_id`, `dni`, `alergias`, `fecha_nacimiento`, `edad`, `sexo`, `ocupacion`, `user_id`, `created`, `modified`)
VALUES
  (2206, '77963248', 'AINES', '2013-01-14', 13, 'M', 'ESTUDIANTE', 2, '2026-06-26 12:48:48', '2026-06-26 12:48:48');

SET FOREIGN_KEY_CHECKS = 1;

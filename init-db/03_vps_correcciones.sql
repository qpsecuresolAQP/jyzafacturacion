-- Correcciones para una base ya instalada en VPS.
-- El script elimina restos de ortodoncia y reacomoda permisos/relaciones.

SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;

-- Limpiar relaciones y permisos de ortodoncia si aun existen en la base.
DELETE rp
FROM roles_permisos rp
INNER JOIN permisos p ON p.id = rp.permiso_id
WHERE p.controller IN ('OrtodonciaControles', 'OrtodonciaInstalaciones');

DELETE up
FROM usuarios_permisos up
INNER JOIN permisos p ON p.id = up.permiso_id
WHERE p.controller IN ('OrtodonciaControles', 'OrtodonciaInstalaciones');

DELETE FROM permisos
WHERE controller IN ('OrtodonciaControles', 'OrtodonciaInstalaciones');

-- Asegurar que no queden filas viejas antes de reinsertar el estado final.
DELETE FROM roles_permisos
WHERE id BETWEEN 380 AND 391;

DELETE FROM usuarios_permisos
WHERE id BETWEEN 67 AND 72;

DELETE FROM permisos
WHERE id BETWEEN 234 AND 239;

DROP TABLE IF EXISTS `ortodoncia_control`;
DROP TABLE IF EXISTS `ortodoncia_instalacion`;

INSERT INTO `permisos` (`id`, `controller`, `action`, `descripcion`, `created`, `modified`) VALUES
(234, 'Recordatorios', 'add', 'Agregar recordatorio', '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(235, 'Recordatorios', 'edit', 'Editar recordatorio', '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(236, 'Recordatorios', 'index', 'Listar recordatorios', '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(237, 'Recordatorios', 'view', 'Ver recordatorio', '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(238, 'Recordatorios', 'delete', 'Eliminar recordatorio', '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(239, 'Recordatorios', 'getByPaciente', 'Obtener recordatorios por paciente', '2026-05-29 00:00:00', '2026-05-29 00:00:00');

INSERT INTO `roles_permisos` (`id`, `rol_id`, `permiso_id`, `created`, `modified`) VALUES
(380, 4, 234, '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(381, 4, 235, '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(382, 4, 236, '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(383, 4, 237, '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(384, 4, 238, '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(385, 4, 239, '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(386, 1, 234, '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(387, 1, 235, '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(388, 1, 236, '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(389, 1, 237, '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(390, 1, 238, '2026-05-29 00:00:00', '2026-05-29 00:00:00'),
(391, 1, 239, '2026-05-29 00:00:00', '2026-05-29 00:00:00');

INSERT INTO `usuarios_permisos` (`id`, `usuario_id`, `permiso_id`, `allow`, `created`) VALUES
(67, 1, 234, 1, '2026-05-29 00:00:00'),
(68, 1, 235, 1, '2026-05-29 00:00:00'),
(69, 1, 236, 1, '2026-05-29 00:00:00'),
(70, 1, 237, 1, '2026-05-29 00:00:00'),
(71, 1, 238, 1, '2026-05-29 00:00:00'),
(72, 1, 239, 1, '2026-05-29 00:00:00');

ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=240;

ALTER TABLE `roles_permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=402;

ALTER TABLE `usuarios_permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=218;

COMMIT;
SET FOREIGN_KEY_CHECKS = 1;
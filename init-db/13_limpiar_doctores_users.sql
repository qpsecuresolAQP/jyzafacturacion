SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE `horarios_doctores`;
TRUNCATE TABLE `doctores`;

DELETE FROM `usuarios_permisos` WHERE `usuario_id` != 1;
DELETE FROM `users` WHERE `id` != 1;

ALTER TABLE `users` AUTO_INCREMENT = 2;

-- Resetear AUTO_INCREMENT de usuarios_permisos al siguiente del max que quedó
SET @next_up = (SELECT IFNULL(MAX(`id`), 0) + 1 FROM `usuarios_permisos`);
SET @sql = CONCAT('ALTER TABLE `usuarios_permisos` AUTO_INCREMENT = ', @next_up);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET FOREIGN_KEY_CHECKS = 1;

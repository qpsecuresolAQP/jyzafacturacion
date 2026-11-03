-- Proveedores identificados por tipo y número de documento (códigos SUNAT catálogo 06):
-- 0 Sin documento, 1 DNI, 4 Carnet de extranjería, 6 RUC, 7 Pasaporte, A Cédula diplomática.
-- La columna `ruc` se mantiene y se sincroniza cuando el tipo es RUC.
ALTER TABLE `proveedores`
  ADD COLUMN `tipo_documento` varchar(1) NOT NULL DEFAULT '0' AFTER `nombre`,
  ADD COLUMN `numero_documento` varchar(20) DEFAULT NULL AFTER `tipo_documento`,
  ADD KEY `idx_proveedores_documento` (`tipo_documento`, `numero_documento`);

UPDATE `proveedores`
SET `tipo_documento` = '6', `numero_documento` = `ruc`
WHERE `ruc` IS NOT NULL AND TRIM(`ruc`) <> '';

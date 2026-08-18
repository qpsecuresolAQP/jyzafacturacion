ALTER TABLE `examenes`
  ADD COLUMN `comision_medico` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `precio_convenio`;

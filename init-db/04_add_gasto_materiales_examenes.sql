-- Agrega el campo de gasto en materiales por examen, para poder calcular
-- la utilidad real: precio cobrado al paciente - convenio laboratorio - comisión médico - gasto en materiales.
ALTER TABLE `examenes`
  ADD COLUMN `gasto_materiales` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `comision_medico`;

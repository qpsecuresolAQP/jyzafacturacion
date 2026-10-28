-- Exámenes sin comisión al médico: se asigna el 5% del precio al paciente,
-- excepto BIOPSIA (6), PRUEBAS MOLECULARES (8) y CITOLOGIA (9).
-- Debe coincidir con ExamenesTable::CATEGORIAS_SIN_COMISION_DEFAULT.
UPDATE `examenes`
SET `comision_medico` = ROUND(`precio` * 0.05, 2)
WHERE (`comision_medico` IS NULL OR `comision_medico` = 0)
  AND `categoria_examen_id` NOT IN (6, 8, 9);
